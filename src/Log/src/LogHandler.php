<?php

declare(strict_types=1);

namespace PCIT\Log;

use App\Build;
use App\Job;
use PCIT\Support\CacheKey;

/**
 * 处理日志.
 */
class LogHandler
{
    private $jobId;

    private $build_id;

    public $cache;

    public function __construct(int $jobId)
    {
        $this->jobId = $jobId;

        $this->build_id = Job::getBuildKeyId($jobId);

        $this->cache = \Cache::store();
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $logs = [];
        $steps = [];

        $steps = $this->getSteps();

        foreach ($steps as $step) {
            $log = $this->handlePipeline($step);

            $logs[$step] = $log;
        }

        $logs = array_filter($logs);
        $logs = json_encode($logs, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        Job::updateLog($this->jobId, $logs);
    }

    public function getSecretPattern(): array
    {
        $secret_value_pattern = [];

        $env_array = \App\Env::list(
            Build::getRid($this->build_id),
            Build::getGitType($this->build_id)
        );

        foreach ($env_array as $k) {
            if ('0' !== $k['public']) {
                continue;
            }
            $secret_value_pattern[] = '/'.preg_quote($k['value'], '/').'/';
        }

        return $secret_value_pattern;
    }

    public function getSteps()
    {
        $types = [
            'pipeline',
            'failure',
            'success',
            'changed',
          ];

        $steps = [];

        foreach ($types as $type) {
            $cacheKey = CacheKey::pipelineListKey($this->jobId, $type);

            $step = array_reverse($this->cache->lrange($cacheKey, 0, -1));

            // $steps = [...$steps, ...$step];
            $steps = array_merge($steps, $step);
        }

        array_unshift($steps, 'clone', 'cache_download');
        array_push($steps, 'cache_upload');

        return $steps;
    }

    public function handlePipeline($pipeline)
    {
        \Log::emergency('📃Handle step log', ['jobId' => $this->jobId, 'step' => $pipeline]);

        $cache = $this->cache;

        // 日志美化
        $log = $cache->hGet(CacheKey::logHashKey($this->jobId), $pipeline);

        if (!$log) {
            \Log::warning('📕Step Log empty, skip', ['jobId' => $this->jobId, 'step' => $pipeline]);

            return;
        }

        // 获取 secret
        $secret_value_pattern = $this->getSecretPattern();
        $log = preg_replace($secret_value_pattern, '***', $log);

        return $log;
    }
}

<?php

declare(strict_types=1);

namespace App\Events;

use App\Job;
use PCIT\Support\CacheKey;

/**
 * 处理日志.
 */
class LogHandler
{
    private $jobId;

    public $cache;

    public function __construct(int $jobId)
    {
        $this->jobId = $jobId;
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
        $logs = json_encode($logs, JSON_THROW_ON_ERROR + JSON_UNESCAPED_UNICODE);

        Job::updateLog($this->jobId, $logs);
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
            $cacheKey = CacheKey::pipelineListKey($this->jobId, $type, 'loghandler');

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
        \Log::emergency('Handle step log', ['jobId' => $this->jobId, 'step' => $pipeline]);

        $cache = $this->cache;

        // 日志美化
        $result = $cache->hGet(CacheKey::logHashKey($this->jobId), $pipeline);

        if (!$result) {
            \Log::warning('Step Log empty, skip', ['jobId' => $this->jobId, 'step' => $pipeline]);

            return;
        }

        $result_array = explode("\n", $result);

        $log = null;

        foreach ($result_array as $line) {
            $line = substr($line, 8);

            $log .= $line."\n";
        }

        return iconv('utf-8', 'utf-8//IGNORE', trim($log));
    }
}

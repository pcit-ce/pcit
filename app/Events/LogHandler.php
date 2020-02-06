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
        $cache = $this->cache;
        $jobId = $this->jobId;

        $steps = $this->getSteps();

        foreach ($steps as $step) {
            $log = $this->handlePipeline($step);

            $logs[$step] = $log;
        }

        $logs = array_filter($logs);

        Job::updateLog($this->jobId, $logs = json_encode($logs, JSON_UNESCAPED_UNICODE));
    }

    public function getSteps()
    {
        $types = [
            'pipeline',
            'failure',
            'success',
            'changed',
          ];

        foreach ($types as $type) {
            $copyKey = CacheKey::pipelineListCopyKey($this->jobId, $type, 'loghandler');

            while (1) {
                $step = $this->cache->rpop($copyKey);

                if (!$step) {
                    break;
                }

                $steps[] = $step;
            }

            $this->cache->del($copyKey);
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

        return trim($log);
    }
}

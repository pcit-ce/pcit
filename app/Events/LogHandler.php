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
            $copyKey = CacheKey::pipelineListCopyKey($this->jobId, $type);

            while (1) {
                $step = $this->cache->rpop($copyKey);

                if (!$step) {
                    break;
                }

                $steps[] = $step;
            }
        }

        array_unshift($steps, 'clone', 'cache_download');
        array_push($steps, 'cache_upload');

        return $steps;
    }

    public function handlePipeline($pipeline)
    {
        $cache = $this->cache;
        // 日志美化
        $result = $cache->hGet(CacheKey::logHashKey($this->jobId), $pipeline);

        if (!$result) {
            \Log::warning('Step Log empty, skip', ['jobId' => $this->jobId, 'step' => $pipeline]);

            return;
        }

        \Log::emergency('Handle step log', ['jobId' => $this->jobId, 'step' => $pipeline]);

        $folder_name = sys_get_temp_dir().'/.pcit';

        !is_dir($folder_name) && mkdir($folder_name);

        file_put_contents($folder_name.'/'.$this->jobId, $result);

        $fh = fopen($folder_name.'/'.$this->jobId, 'r');

        $redis_key = (string) $this->jobId.'_log';

        $cache->del($redis_key);

        while (!feof($fh)) {
            $one_line_content = fgets($fh);

            $one_line_content = substr("$one_line_content", 8);

            $cache->append($redis_key, $one_line_content);
        }

        fclose($fh);

        $log_content = $cache->get($redis_key);

        // cleanup
        $this->gc($folder_name, $redis_key);

        try {
            $log_content = iconv('utf-8', 'utf-8//IGNORE', $log_content);
        } catch (\Throwable $e) {
            \Log::emergency('iconv handle error '.$e->getMessage(), ['pipeline' => $k]);
        }

        return $log_content;
    }

    private function gc(string $folder_name, string $redis_key): void
    {
        unlink($folder_name.'/'.$this->jobId);
        $this->cache->del($redis_key);
    }
}

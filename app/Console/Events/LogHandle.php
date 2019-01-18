<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Job;
use PCIT\Support\Cache;
use PCIT\Support\CacheKey;
use PCIT\Support\Log;

/**
 * 处理日志.
 */
class LogHandle
{
    private $jobId;

    public function __construct(int $jobId)
    {
        $this->jobId = $jobId;
        $this->cache = Cache::store();
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $logs = [];
        $pipelines = [];
        $cache = $this->cache;
        $jobId = $this->jobId;

        $types = [
          'pipeline',
          'failure',
          'success',
          'changed',
        ];

        foreach ($types as $type) {
            $copyKey = CacheKey::pipelineListCopyKey($jobId, $type);

            while (1) {
                $pipeline = $cache->rpop($copyKey);

                if (!$pipeline) {
                    break;
                }

                $pipelines[] = $pipeline;
            }
        }

        array_unshift($pipelines, 'clone', 'cache_download');
        array_push($pipelines, 'cache_upload');

        foreach ($pipelines as $pipeline) {
            $log = $this->handlePipeline($pipeline);

            $logs[$pipeline] = $log;
        }

        $logs = array_filter($logs);

        // 处理特殊字符
        foreach ($logs as $k => $v) {
            try {
                $result = iconv('utf-8', 'utf-8//IGNORE', $v);
                $logs[$k] = $result;
            } catch (\Throwable $e) {
                Log::debug(__FILE__, __LINE__,
                'iconv handle error '.$e->getMessage(), ['pipeline' => $k],
                Log::EMERGENCY);
            }
        }

        Job::updateLog($this->jobId, $logs = json_encode($logs, JSON_UNESCAPED_UNICODE));
    }

    private function handlePipeline($pipeline)
    {
        $cache = $this->cache;
        // 日志美化
        $result = $cache->hGet(CacheKey::logHashKey($this->jobId), $pipeline);

        if (!$result) {
            Log::debug(__FILE__, __LINE__,
                'job Log empty, skip', ['jobId' => $this->jobId], Log::WARNING);

            return;
        }

        Log::debug(__FILE__, __LINE__,
            'Handle job log', ['jobId' => $this->jobId, 'pipeline' => $pipeline],
            Log::EMERGENCY);

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

        return $log_content;
    }

    private function gc(string $folder_name, string $redis_key): void
    {
        unlink($folder_name.'/'.$this->jobId);
        $this->cache->del($redis_key);
    }
}

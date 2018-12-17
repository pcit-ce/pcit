<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Job;
use PCIT\Support\Cache;
use PCIT\Support\Log;

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
            $cache->del('pcit/'.$jobId.'/'.$type.'/list_copy');

            $cache->restore('pcit/'.$jobId.'/'.$type.'/list_copy',
              0,
              $cache->dump('pcit/'.$jobId.'/'.$type.'/list'));
        }

        foreach ($types as $type) {
            while (1) {
                $pipeline = $cache->rpop('pcit/'.$jobId.'/'.$type.'/list_copy');

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

        Job::updateLog($this->jobId, $logs = json_encode($logs, JSON_UNESCAPED_UNICODE));
    }

    private function handlePipeline($pipeline)
    {
        $cache = $this->cache;
        // 日志美化
        $output = $cache->hGet(
            'pcit/'.$this->jobId.'/build_log', $pipeline);

        if (!$output) {
            Log::debug(__FILE__, __LINE__,
                'job Log empty, skip', ['jobId' => $this->jobId], Log::WARNING);

            return;
        }

        Log::debug(__FILE__, __LINE__,
            'Handle job log', ['jobId' => $this->jobId, 'pipeline' => $pipeline],
            Log::EMERGENCY);

        $folder_name = sys_get_temp_dir().'/.pcit';

        !is_dir($folder_name) && mkdir($folder_name);

        file_put_contents($folder_name.'/'.$this->jobId, $output);

        $fh = fopen($folder_name.'/'.$this->jobId, 'rb');

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

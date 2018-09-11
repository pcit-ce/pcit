<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Job;
use KhsCI\Support\Cache;
use KhsCI\Support\Log;

class LogHandle
{
    private $job_id;

    public function __construct(int $job_id)
    {
        $this->job_id = $job_id;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        // 日志美化
        $output = Cache::store()->hGet('build_log', (string) $this->job_id);

        if (!$output) {
            Log::debug(__FILE__, __LINE__,
                'job Log empty, skip', ['job_id' => $this->job_id], Log::WARNING);

            return;
        }

        Log::debug(__FILE__, __LINE__,
            'Handle job log', ['job_id' => $this->job_id], Log::EMERGENCY);

        $folder_name = sys_get_temp_dir().'/.khsci';

        !is_dir($folder_name) && mkdir($folder_name);

        file_put_contents($folder_name.'/'.$this->job_id, "$output");

        $fh = fopen($folder_name.'/'.$this->job_id, 'rb');

        $redis_key = (string) $this->job_id.'_log';

        Cache::store()->del($redis_key);

        while (!feof($fh)) {
            $one_line_content = fgets($fh);

            $one_line_content = substr("$one_line_content", 8);

            Cache::store()->append($redis_key, $one_line_content);
        }

        fclose($fh);

        $log_content = Cache::store()->get($redis_key);

        Job::updateLog($this->job_id, $log_content);

        // cleanup
        unlink($folder_name.'/'.$this->job_id);

        Cache::store()->del($redis_key);
    }
}

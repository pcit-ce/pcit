<?php

declare(strict_types=1);

namespace App\Console\BuildFunction;

use App\Job;
use KhsCI\Support\Cache;
use KhsCI\Support\Log;

class LogHandle
{
    /**
     * @var Build
     */
    private $build;

    public function __construct(Build $build)
    {
        $this->build = $build;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $build = $this->build;

        // 日志美化
        $output = Cache::store()->hGet('build_log', (string) $build->build_key_id);

        if (!$output) {
            Log::debug(__FILE__, __LINE__, 'Build Log empty, skip', [], Log::WARNING);

            return;
        }

        if (!$build->unique_id) {
            Log::debug(__FILE__, __LINE__, 'config not found, skip', [], Log::WARNING);

            return;
        }

        $folder_name = sys_get_temp_dir().'/.khsci';

        !is_dir($folder_name) && mkdir($folder_name);

        file_put_contents($folder_name.'/'.$build->unique_id, "$output");

        $fh = fopen($folder_name.'/'.$build->unique_id, 'r');

        Cache::store()->del((string) $build->unique_id);

        while (!feof($fh)) {
            $one_line_content = fgets($fh);

            $one_line_content = substr("$one_line_content", 8);

            Cache::store()->append((string) $build->unique_id, $one_line_content);
        }

        fclose($fh);

        $log_content = Cache::store()->get((string) $build->unique_id);

        Job::updateLog($build->build_key_id, $log_content);

        // cleanup
        unlink($folder_name.'/'.$build->unique_id);

        Cache::store()->del((string) $build->unique_id);
    }
}

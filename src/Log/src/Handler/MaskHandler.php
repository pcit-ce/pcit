<?php

declare(strict_types=1);

namespace PCIT\Log\Handler;

class MaskHandler
{
    public function handle(string $log, int $line_offset = 0, array $mask_value_array = []): array
    {
        $new_log = [];

        $line_array = explode("\n", $log);

        $hide_value_array = [];

        foreach ($line_array as $line) {
            $matches = [];

            $line_start = substr($line, 0, $line_offset);
            $line_content = substr($line, $line_offset) ?: '';

            preg_match('/^::add-mask::/', $line_content, $matches);

            if ($matches) {
                $hide_value = substr($line_content, 12);

                $hide_value = str_replace('%0A', "\n", $hide_value);
                $hide_value = str_replace('%0D', "\r", $hide_value);
                $hide_value = str_replace('%25', '%', $hide_value);

                $hide_value_array[] = $hide_value;

                continue;
            }
            $new_log[] = $line_start.$line_content;
        }

        $log = implode("\n", $new_log);

        // 合并当前 log 中的 mask 和以前的 mask
        $mask_value_array = array_merge($hide_value_array, $mask_value_array);

        $log = $this->hideValue($log, $mask_value_array);

        // 返回 log 和当前 log 中的 mask
        return [$log, $hide_value_array];
    }

    public function hideValue(string $log, array $mask_value_array = []): string
    {
        // replace hide value
        $hide_value_pattern = [];

        foreach ($mask_value_array as $item) {
            // $item = str_replace('%0A', '%0A%0A', $item);
            // var_dump($item);
            $hide_value_pattern[] = '/'.preg_quote($item, '/').'/';
        }

        // $log = str_replace('%', '%25', $log);
        // $log = str_replace("\n", '%0A', $log);
        // $log = str_replace("\r", '%0D', $log);

        return preg_replace($hide_value_pattern, '***', $log);
        // file_put_contents('1.txt', $log, FILE_APPEND);

        // $log = str_replace('%0A', "\n", $log);
        // $log = str_replace('%0D', "\r", $log);
        // $log = str_replace('%25', '%', $log);
    }
}

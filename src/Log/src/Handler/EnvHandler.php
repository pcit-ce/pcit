<?php

declare(strict_types=1);

namespace PCIT\Log\Handler;

class EnvHandler
{
    public function handle(string $log, int $line_offset = 0): array
    {
        $new_log = [];

        $line_array = explode("\n", $log);

        $env = [];

        foreach ($line_array as $line) {
            $matches = [];

            $line_start = substr($line, 0, $line_offset);
            $line_content = substr($line, $line_offset) ?: '';

            preg_match('/^::set-env name=/', $line_content, $matches);

            if ($matches) {
                $line_content = substr($line_content, 15);
                [$env_key,$env_value] = explode('::', $line_content, 2);

                $env_value = str_replace('%0A', "\n", $env_value);
                $env_value = str_replace('%0D', "\r", $env_value);
                $env_value = str_replace('%25', '%', $env_value);

                $env[$env_key] = $env_value;
                continue;
            } else {
                $new_log[] = $line_start.$line_content;
            }
        }

        return [implode("\n", $new_log), $env];
    }
}

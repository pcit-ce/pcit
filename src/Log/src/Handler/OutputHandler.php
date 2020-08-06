<?php

declare(strict_types=1);

namespace PCIT\Log\Handler;

class OutputHandler
{
    public function handle(string $log, int $line_offset = 0): array
    {
        $new_log = [];

        $line_array = explode("\n", $log);

        $output = [];

        foreach ($line_array as $line) {
            $matches = [];

            $line_start = substr($line, 0, $line_offset);
            $line_content = substr($line, $line_offset) ?: '';

            preg_match('/^::set-output name=/', $line_content, $matches);

            if ($matches) {
                $line_content = substr($line_content, 18);
                [$key,$value] = explode('::', $line_content, 2);

                $value = str_replace('%0A', "\n", $value);
                $value = str_replace('%0D', "\r", $value);
                $value = str_replace('%25', '%', $value);

                $output[$key] = $value;

                continue;
            }
            $new_log[] = $line_start.$line_content;
        }

        return [implode("\n", $new_log), $output];
    }
}

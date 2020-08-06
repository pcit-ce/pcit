<?php

declare(strict_types=1);

namespace PCIT\Log\Handler;

use PCIT\Runner\Events\Handler\EnvHandler;

class AnsiHandler
{
    private $context = [
    ];

    /**
     * @param int $line_offset 日志正文内容的偏移量，例如每行日志正文内容前有时间，
     *                         该参数为了将时间去掉，获取日志内容
     */
    public function handle(
        string $log,
        int $line_offset = 0,
        string $type = 'warning',
        string $color_header = '[33m'
    ): array {
        $new_log = [];

        $line_array = explode("\n", $log);

        foreach ($line_array as $line) {
            $matches = [];

            // 日志时间
            $line_start = substr($line, 0, $line_offset);
            $line_content = substr($line, $line_offset) ?: '';

            preg_match('/^::'.$type.'/', $line_content, $matches);

            if ($matches) {
                [,$context,$message] = explode('::', $line_content, 3);
                $this->handleContext($context);
                // $line_content = substr($line_content, \strlen($pattern));

                $message = str_replace('%0A', "\n$line_start$color_header", $message);
                $message = str_replace('%0D', "\r", $message);
                $message = str_replace('%25', '%', $message);

                $log_content = $color_header.'##['.$type.']'.$message.'[0m';
            } else {
                $log_content = $line_content;
            }

            $new_log[] = $line_start.$log_content;
        }

        return [implode("\n", $new_log), $this->context];
    }

    /**
     * ::warning file={name},line={line},col={col}::{message}
     *   warning file={name},line={line},col={col}.
     *
     * ::warning::{message}
     *   'warning'
     */
    public function handleContext(string $context): void
    {
        $context_array = explode(' ', $context, 2);

        if (!($context_array[1] ?? false)) {
            return;
        }

        // file={name},line={line},col={col}

        $context_array = explode(',', $context_array[1], 4);

        $this->context[] = (new EnvHandler())->array2obj($context_array);
    }
}

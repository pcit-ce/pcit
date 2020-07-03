<?php

declare(strict_types=1);

namespace PCIT\Log\Handler;

class DebugHandler
{
    public function handle(string $log, int $line_offset = 0): array
    {
        return (new AnsiHandler())->handle(
             $log,
             $line_offset,
             'debug',
             '[32m'
            );
    }
}

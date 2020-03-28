<?php

declare(strict_types=1);

namespace PCIT\Plugin\Toolkit;

class Exec
{
    public function exec(string $command): void
    {
        echo '[36m[command]'.$command.'[0m'.PHP_EOL;
    }
}

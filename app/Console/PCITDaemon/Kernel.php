<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

abstract class Kernel
{
    abstract public function handle();
}

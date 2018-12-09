<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use PCIT\PCIT;

abstract class Kernel
{
    public $pcit;

    public function __construct()
    {
        $this->pcit = app(PCIT::class);
    }

    abstract public function handle();
}

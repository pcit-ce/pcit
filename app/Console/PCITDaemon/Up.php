<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

class Up extends Kernel
{
    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        (new Server())->handle();
        (new Agent())->handle();
    }
}

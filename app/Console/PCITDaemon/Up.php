<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

class Up extends Kernel
{
    /**
     * Up 命令可以在一个节点同时运行 server 和 agent 节点.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        (new Server())->handle();
        (new Agent())->handle();
    }
}

<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon\Commands;

use App\Console\PCITDaemon\Server;

class ServerCommand extends Kernel
{
    public function configure(): void
    {
        $this->handler = new Server();
        $this->setName('server');
        $this->setDescription('Run PCIT server daemon');
    }
}

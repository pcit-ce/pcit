<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon\Commands;

use App\Console\PCITDaemon\Agent;

class AgentCommand extends Kernel
{
    public function configure(): void
    {
        $this->setName('agent');
        $this->setDescription('Run PCIT agent daemon');
        $this->handler = new Agent();
    }
}

<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon\Commands;

use App\Console\PCITDaemon\Up;

class UpCommand extends Kernel
{
    public function configure(): void
    {
        $this->setName('up');
        $this->setDescription('Run PCIT server and agent daemon on one node');
        $this->handler = new Up();
    }
}

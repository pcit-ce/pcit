<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AgentCommand extends Command
{
    public function configure(): void
    {
        $this->setName('agent');
        $this->setDescription('Run PCIT agent Daemon');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        (new Agent())->handle();
    }
}

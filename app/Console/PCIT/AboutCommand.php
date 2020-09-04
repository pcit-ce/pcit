<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    public function configure(): void
    {
        $this->setName('about');
        $this->setDescription('Shows the short information about PCIT');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>PCIT - Test, Build & Deploy Code</info>

<comment>PCIT is a CI/CD system based Docker and TencentAI.

See <info>https://ci.khs1994.com</info> for more information.</comment>
        ');

        return 0;
    }
}

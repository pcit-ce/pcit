<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WhoamiCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('whoami');

        $this->setDescription('Outputs the current user');

        $this->addOption(...PCITCommand::getGitTypeOptionArray());

        $this->addOption(...PCITCommand::getAPIEndpointOptionArray());

        $this->addOption(...PCITCommand::getRawOptionArray());
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(PCITCommand::HttpGet($input, 'user', null, true));
    }
}

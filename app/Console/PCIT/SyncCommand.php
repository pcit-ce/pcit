<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('sync');

        $this->setDescription('Triggers a new sync with Git');

        $this->addOption(...PCITCommand::getGitTypeOptionArray());

        $this->addOption(...PCITCommand::getAPIEndpointOptionArray());
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        PCITCommand::HttpPost($input, 'user/sync', null, true);

        $output->writeln('Sync User info Success');

        return 0;
    }
}

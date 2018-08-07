<?php

declare(strict_types=1);

namespace App\Console\Khsci;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('sync');

        $this->setDescription('Triggers a new sync with Git');

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        KhsCICommand::HttpPost($input, 'user/sync', null, true);

        $output->writeln('Sync User info Success');
    }
}

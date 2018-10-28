<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('migrate');
        $this->setDescription('Migrate database');

        $this->addArgument('sql_file', null, 'New database by SQL file');

        $this->addOption('all', null, null, 'New all database');
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
        $sql_file = $input->getArgument('sql_file');

        if ($sql_file) {
            Migrate::migrate($sql_file);

            return;
        }

        if ($input->getOption('all')) {
            Migrate::all();
        }
    }
}

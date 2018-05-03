<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrations extends Command
{
    protected function configure()
    {
        $this->setName('migration');
        $this->setDescription('Migration database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump($input);
    }
}

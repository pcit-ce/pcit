<?php

declare(strict_types=1);

namespace App\Console\Khsci;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Whoami extends Command
{
    protected function configure(): void
    {
        $this->setName('whoami');

        $this->setDescription('Outputs the current user');

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(KhsCICommand::HttpGet($input, 'user', null, true));
    }
}

<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptFileCommand extends Command
{
    public function configure(): void
    {
        $this->setName('encrypt-file');
        $this->setDescription('Encrypts a file');
        $this->addArgument('file', InputArgument::REQUIRED, 'file name');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = getcwd().\DIRECTORY_SEPARATOR.$input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln('<error>file not exists</error>');

            return 1;
        }

        $file_contents = file_get_contents($file);

        $output->writeln(addcslashes($file_contents, "\n"));

        return 0;
    }
}

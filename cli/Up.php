<?php

declare(strict_types=1);

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Up extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('up');

        $this->setDescription('Run KhsCI Daemon');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        \App\Console\Migrate::all();

        \App\Console\Up::up();
    }
}

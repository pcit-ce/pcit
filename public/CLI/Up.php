<?php

namespace CLI;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Up extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('up');

        $this->setDescription('Run KhsCI Daemon');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \App\Console\Up::up();
    }
}

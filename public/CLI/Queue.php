<?php

namespace CLI;

use KhsCI\Service\Queue\Queue as QueueService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Queue extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('queue');
        $this->setDescription('Run queue');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = new QueueService();

        $queue();
    }


}

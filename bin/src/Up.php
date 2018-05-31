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
        try {
            sleep(5);

            \App\Console\Migrate::all();
        } catch (Exception $e) {
            sleep(30);
            \App\Console\Migrate::all();
        }

        \KhsCI\Support\Log::debug(__FILE__, __LINE__, "Start Memory is ".memory_get_usage());

        while (1) {
            $up = new \App\Console\Up();
            $up->up();
            unset($up);
            \KhsCI\Support\Log::debug(__FILE__, __LINE__, "Now Memory is ".memory_get_usage());
            sleep(3);
        }
    }
}

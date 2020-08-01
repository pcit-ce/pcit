<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use App\Http\Controllers\Plugins\Metadata as PluginMetadate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('plugin');

        $this->setDescription('generate plugin metadata json file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new PluginMetadate())();

        $output->write('<info>generate plugin metadata json file success</info>');

        return 0;
    }
}

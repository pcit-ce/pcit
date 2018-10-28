<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class HistoryCommand extends Command
{
    public function configure(): void
    {
        $this->setName('history');
        $this->setDescription('Displays a projects build history');
    }
}

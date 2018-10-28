<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class ShowCommand extends Command
{
    public function configure(): void
    {
        $this->setName('show');
        $this->setDescription('Displays a build or job');
    }
}

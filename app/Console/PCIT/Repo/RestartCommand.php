<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class RestartCommand extends Command
{
    public function configure(): void
    {
        $this->setName('restart');
        $this->setDescription('Restarts a build or job');
    }
}

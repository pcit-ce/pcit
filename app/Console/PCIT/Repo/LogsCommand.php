<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class LogsCommand extends Command
{
    public function configure(): void
    {
        $this->setName('logs');
        $this->setDescription('Streams test logs');
    }
}

<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class StatusCommand extends Command
{
    public function configure(): void
    {
        $this->setName('status');
        $this->setDescription('Checks status of the latest build');
    }
}

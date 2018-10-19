<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class CancelCommand extends Command
{
    public function configure(): void
    {
        $this->setName('cancel');
        $this->setDescription('Cancels a job or build');
    }
}

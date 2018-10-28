<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class RequestsCommand extends Command
{
    public function configure(): void
    {
        $this->setName('requests');
        $this->setDescription('Lists recent requests');
    }
}

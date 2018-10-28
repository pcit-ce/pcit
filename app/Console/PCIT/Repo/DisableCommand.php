<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class DisableCommand extends Command
{
    public function configure(): void
    {
        $this->setName('disable');
        $this->setDescription('Disables a project');
    }
}

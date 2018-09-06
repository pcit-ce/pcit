<?php

declare(strict_types=1);

namespace App\Console\KhsCI\Repo;

use Symfony\Component\Console\Command\Command;

class EnableCommand extends Command
{
    public function configure(): void
    {
        $this->setName('enable');
        $this->setDescription('Enables a project');
    }
}

<?php

declare(strict_types=1);

namespace App\Console\KhsCI\Repo;

use Symfony\Component\Console\Command\Command;

class BranchesCommand extends Command
{
    public function configure(): void
    {
        $this->setName('branches');
        $this->setDescription('Displays the most recent build for each branch');
    }
}

<?php

declare(strict_types=1);

namespace App\Console\KhsCI\Repo;

use Symfony\Component\Console\Command\Command;

class ReposCommand extends Command
{
    public function configure(): void
    {
        $this->setName('repos');
        $this->setDescription('Lists repositories the user has certain permissions on');
    }
}

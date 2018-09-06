<?php

declare(strict_types=1);

namespace App\Console\KhsCI\Repo;

use Symfony\Component\Console\Command\Command;

class CacheCommand extends Command
{
    public function configure(): void
    {
        $this->setName('cache');
        $this->setDescription('Lists or deletes repository caches');
    }
}

<?php

declare(strict_types=1);

namespace App\Console\KhsCI;

use Symfony\Component\Console\Command\Command;

class AccountsCommand extends Command
{
    public function configure(): void
    {
        $this->setName('accounts');

        $this->setDescription('Displays accounts and their subscription status');
    }
}

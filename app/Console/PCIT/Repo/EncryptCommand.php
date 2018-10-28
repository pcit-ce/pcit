<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use Symfony\Component\Console\Command\Command;

class EncryptCommand extends Command
{
    public function configure(): void
    {
        $this->setName('encrypt');
        $this->setDescription('Encrypts values for the .pcit.yml');
    }
}

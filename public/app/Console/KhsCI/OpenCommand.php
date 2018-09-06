<?php

declare(strict_types=1);

namespace App\Console\KhsCI;

use Symfony\Component\Console\Command\Command;

class OpenCommand extends Command
{
    public function configure(): void
    {
        $this->setName('open');
        $this->setDescription('Opens a build or job in the browser');
    }
}

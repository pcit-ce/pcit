<?php

declare(strict_types=1);

namespace PCIT\Runner\Conditional;

class Status extends Kernel
{
    public function handle(bool $reg = false): bool
    {
        if (!$this->conditional) {
            return false;
        }

        return parent::handle($reg);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Runner\Conditional;

class Matrix extends Kernel
{
    public function handle()
    {
        if (!$this->conditional) {
            return true;
        }

        if ($this->current === (array) ($this->conditional)[0]) {
            return true;
        }

        return false;
    }
}

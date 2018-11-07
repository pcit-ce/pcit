<?php

namespace PCIT\Service\Build\Conditional;


class Matrix extends Kernel
{
    public function handle()
    {
        if ($this->current === $this->conditional) {
            return true;
        }

        return false;
    }
}

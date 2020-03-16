<?php

declare(strict_types=1);

namespace PCIT\Runner\Conditional;

use PCIT\Framework\Support\ArrayHelper;

class Matrix extends Kernel
{
    public function handle(bool $reg = false): bool
    {
        if (!$this->conditional) {
            return true;
        }

        $exclude = false;

        $current = (array) $this->current;

        // include
        if ($this->conditional->include ?? false) {
            $this->conditional = $this->conditional->include;
        }

        // exclude
        if ($this->conditional->exclude ?? false) {
            $this->conditional = $this->conditional->exclude;
            $exclude = true;
        }

        foreach ($this->conditional as $conditional) {
            if ($exclude) {
                if (ArrayHelper::compare($current, (array) $conditional)) {
                    return false;
                }
            } else {
                if (ArrayHelper::compare($current, (array) $conditional)) {
                    return true;
                }
            }
        }

        // false !== false // false
        // false !== true // true
        return false !== $exclude;
    }
}

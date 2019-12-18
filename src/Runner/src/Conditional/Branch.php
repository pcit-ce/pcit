<?php

declare(strict_types=1);

namespace PCIT\Runner\Conditional;

class Branch extends Kernel
{
    public function regHandle()
    {
        if (!\is_object($this->conditional)) {
            return parent::regHandle();
        }

        $include = $this->conditional->include ?? [];
        $exclude = $this->conditional->exclude ?? [];

        if ($exclude) {
            $this->conditional = $exclude;
            $result = $this->regHandle();

            if (true === ($result ?? false)) {
                return false;
            } else {
                return true;
            }
        }

        if ($include) {
            $this->conditional = $include;
            $result = $this->regHandle();

            if (true === ($result ?? false)) {
                return true;
            }
        }

        return false;
    }
}

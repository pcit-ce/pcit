<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Conditional;

class Branch extends Kernel
{
    public function regHandle()
    {
        if (!\is_object($this->conditional)) {
            return parent::reghandle();
        }

        $include = $this->conditional->include ?? [];
        $exclude = $this->conditional->exclude ?? [];

        if ($exclude) {
            $this->conditional = $exclude;
            $result = $this->RegHandle();

            if (true === ($result ?? false)) {
                return false;
            }
        }

        if ($include) {
            $this->conditional = $include;
            $result = $this->RegHandle();

            if (true === ($result ?? false)) {
                return true;
            }
        }

        return false;
    }
}

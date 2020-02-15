<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use PCIT\Framework\Contracts\Cache\Store;

class NoneStore implements Store
{
    public function __call($method, $arguments): void
    {
    }
}

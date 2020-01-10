<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use PCIT\Framework\Contracts\Cache\Repository as CacheContracts;
use PCIT\Framework\Contracts\Cache\Store;

class Repository implements CacheContracts
{
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __call($method, $arguments)
    {
        return $this->store->$method(...$arguments);
    }

    public function getStore()
    {
        return $this->store;
    }
}

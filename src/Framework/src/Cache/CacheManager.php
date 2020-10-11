<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use PCIT\Framework\Contracts\Cache\Factory;
use Redis;

class CacheManager implements Factory
{
    protected $stores = [];

    /**
     * @return \PCIT\Framework\Contracts\Cache\Repository
     */
    public function store(?string $name = null)
    {
        $name = $name ?: config('cache.default');

        if ($store = $this->stores[$name] ?? false) {
            return $store;
        }

        if ('file' === $name) {
            $store = new FileStore();
        } elseif ('none' === $name) {
            $store = new NoneStore();
        } else {
            $store = new RedisStore(new Redis());
        }

        $repository = new Repository($store);

        return $this->stores[$name] = $repository;
    }

    public function close(): void
    {
        $this->stores = [];
    }

    public function __call($method, $arguments)
    {
        return $this->store()->$method(...$arguments);
    }
}

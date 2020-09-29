<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use Exception;
use PCIT\Framework\Contracts\Cache\Store;
use Redis;

class RedisStore implements Store
{
    public Redis $redis;

    public function __construct(Redis $redis)
    {
        try {
            $redis->connect(config('cache.stores.redis.host'), (int) config('cache.stores.redis.port'));
            $redis->getLastError();
            $redis->select((int) config('cache.stores.redis.database'));
        } catch (Exception $e) {
            throw new Exception("Can't connect Redis server, error code ".$e->getCode(), 500);
        }

        $this->redis = $redis;
    }

    public function __call($method, $arguments)
    {
        return $this->redis->$method(...$arguments);
    }

    public function copyListKey($source, $target): string
    {
        \Cache::restore($target, 0, \Cache::dump($source));

        return $target;
    }
}

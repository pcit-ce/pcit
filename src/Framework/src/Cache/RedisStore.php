<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use Exception;
use PCIT\Framework\Contracts\Cache\Store;
use Redis;

class RedisStore implements Store
{
    private $redis;

    public function __construct(Redis $redis)
    {
        try {
            $redis->connect(env('CI_REDIS_HOST', 'redis'), (int) env('CI_REDIS_PORT', 6379));
            $redis->getLastError();
            $redis->select((int) env('CI_REDIS_DATABASE', 16));
        } catch (Exception $e) {
            throw new Exception("Can't connect Redis server, error code ".$e->getCode(), 500);
        }

        $this->redis = $redis;
    }

    public function __call($method, $arguments)
    {
        return $this->redis->$method(...$arguments);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use Exception;
use PCIT\Framework\Contracts\Cache\Repository;
use Redis;

class Cache implements Repository
{
    /**
     * @var Redis
     */
    private static $cache;

    protected $stores;

    /**
     * @return Redis
     *
     * @throws Exception
     */
    public function store($name = 'redis')
    {
        if (self::$cache) {
            return self::$cache;
        }

        if ('file' === $name) {
            return self::$cache = new FileStore();
        }

        $redis = new Redis();

        $this->stores[$name] = $redis;

        try {
            $redis->connect(env('CI_REDIS_HOST', 'redis'), (int) env('CI_REDIS_PORT', 6379));
            $redis->getLastError();
            $redis->select((int) env('CI_REDIS_DATABASE', 16));
        } catch (Exception $e) {
            throw new Exception("Can't connect Redis server, error code ".$e->getCode(), 500);
        }

        return self::$cache = $redis;
    }

    public function close(): void
    {
        if (self::$cache instanceof Redis) {
            self::$cache->close();
            self::$cache = null;

            return;
        }

        self::$cache = null;
    }

    public function __call($method, $arguments)
    {
        return $this->store()->$method(...$arguments);
    }
}

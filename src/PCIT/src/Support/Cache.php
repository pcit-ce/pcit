<?php

declare(strict_types=1);

namespace PCIT\Support;

use Exception;
use Redis;

class Cache
{
    /**
     * @var Redis
     */
    private static $cache;

    /**
     * @return Redis
     *
     * @throws Exception
     */
    public static function store()
    {
        if (!(self::$cache instanceof Redis)) {
            $redis = new Redis();

            try {
                $redis->connect(env('CI_REDIS_HOST', 'redis'), (int) env('CI_REDIS_PORT', 6379));
                $redis->getLastError();
                $redis->select((int) env('CI_REDIS_DATABASE', 16));
            } catch (Exception $e) {
                throw new Exception("Can't connect Redis server, error code ".$e->getCode(), 500);
            }

            self::$cache = $redis;
        }

        return self::$cache;
    }

    public static function close(): void
    {
        if (self::$cache instanceof Redis) {
            self::$cache->close();
            self::$cache = null;
        }
    }
}

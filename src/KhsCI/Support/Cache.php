<?php

declare(strict_types=1);

namespace KhsCI\Support;

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
    public static function connect()
    {
        if (!(self::$cache instanceof Redis)) {
            $redis = new Redis();

            try {
                $redis->connect(Env::get('CI_REDIS_HOST', '127.0.0.1'), (int) Env::get('CI_REDIS_PORT', 6379));
                $redis->getLastError();
            } catch (Exception $e) {
                throw new Exception("Can't connect Redis server, error code ".$e->getCode(), 500);
            }

            self::$cache = $redis;
        }

        return self::$cache;
    }

    public static function close()
    {
        if (self::$cache instanceof Redis) {
            self::$cache->close();
            self::$cache = null;
        }
    }
}

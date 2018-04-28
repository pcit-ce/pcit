<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Exception;
use Redis;

class Cache
{
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
                $redis->connect(Env::get('REDIS_HOST', '127.0.0.1'), (int) Env::get('REDIS_PORT', 6379));
                $redis->getLastError();
            } catch (Exception $e) {
                throw new Exception("Can't connect Redis server, error code ".$e->getCode(), 500);
            }

            self::$cache = $redis;
        }

        return self::$cache;
    }
}

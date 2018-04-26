<?php

namespace KhsCI\Support;

class Cache
{
    private static $cache;

    public static function connect()
    {
        if (!(self::$cache instanceof \Redis)) {
            $redis = new \Redis();
            $redis->connect(getenv('REDIS_HOST'));

            self::$cache = $redis;
        }

        return self::$cache;
    }

}

<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static string|mixed|bool get(string $key)
 * @method static bool set(string $key, mixed|string $value, array|int $timeout = null)
 * @method static bool setex(string $key, int $ttl, mixed|string $value)
 * @method static bool psetex(string $key, int $ttl, mixed|string $value)
 * @method static bool setnx(string $key, mixed|string $value)
 * @method static bool del(array|int|string $key1, int|string ...$otherKeys)
 * @method static bool delete(array|int|string $key1, int|string ...$otherKeys)
 * @method static int|bool exists(string|string[] $key)
 * @method static int   incr(string $key)
 * @method static float incrByFloat(string $key, float $increment)
 * @method static int   incrBy(string $key, int $value)
 * @method static int   decr(string $key)
 * @method static int   decrBy(string $key, int $value)
 * @method static int|bool lPush(string $key, string|mixed ...$value1)
 * @method static int|bool rPush(string $key, string|mixed ...$value1)
 * @method static int|bool lPushx(string $key, mixed|string $value)
 * @method static int|bool rPushx(string $key, mixed|string $value)
 * @method static mixed|bool lPop(string $key)
 * @method static mixed|bool rPop(string $key)
 * @method static int|bool lLen(string $key)
 * @method static int|bool lSize(string $key)
 * @method static mixed|bool lIndex(string $key, int $index)
 * @method static mixed|bool lGet(string $key, int $index)
 * @method static bool       lSet(string $key, int $index, string $value)
 * @method static array      lRange(string $key, int $start, int $end)
 * @method static array      lGetRange(string $key, int $start, int $end)
 * @method static array|bool lTrim(string $key, int $start, int $stop)
 * @method static array|bool listTrim(string $key, int $start, int $stop)
 * @method static int|bool   lRem(string $key, string $value, int $count)
 * @method static int|bool   lRemove(string $key, string $value, int $count)
 * @method static int        lInsert(string $key, int $position, string $pivot, mixed|string $value)
 * @method static string|mixed getSet(string $key, mixed|string $value)
 * @method static bool select(int $dbIndex)
 * @method static bool move(string $key, int $dbIndex)
 * @method static bool rename(string $srcKey, string $dstKey)
 * @method static bool renameKey(string $srcKey, string $dstKey)
 * @method static bool renameNx(string $srcKey, string $dstKey)
 * @method static bool expire(string $key, int $ttl)
 * @method static bool pExpire(string $key, int $ttl)
 * @method static bool setTimeout(string $key, int $ttl)
 * @method static bool expireAt(string $key, int $timestamp)
 * @method static bool pExpireAt(string $key, int $timestamp)
 * @method static string[] keys(string $pattern)
 * @method static string[] getKeys(string $pattern)
 * @method static int      dbSize()
 * @method static bool     auth(string $password)
 * @method static int      type(string $key)
 * @method static string|false dump(string $key)
 * @method static bool         restore(string $key, int $ttl, string $value)
 *
 * @see https://github.com/JetBrains/phpstorm-stubs/blob/master/redis/Redis.php
 */
class Cache extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'cache';
    }
}

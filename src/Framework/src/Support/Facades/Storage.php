<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static bool has(string $path)
 * @method static string|false read(string $path)
 * @method static resource|false readStream(string $path)
 * @method static array listContents(string $directory = '', bool $recursive = false)
 * @method static array|false getMetadata(string $path)
 * @method static int|false getSize(string $path)
 * @method static string|false getMimetype(string $path)
 * @method static string|false getTimestamp(string $path)
 * @method static string|false getVisibility(string $path)
 * @method static bool write(string $path, string $contents, array $config = [])
 * @method static bool writeStream(string $path, resource $resource, array $config = [])
 * @method static bool update(string $path, string $contents, array $config = [])
 * @method static bool updateStream(string $path, resource $resource, array $config = [])
 * @method static bool rename(string $path, string $newpath)
 * @method static bool copy(string $path, string $newpath)
 * @method static bool delete(string $path)
 * @method static bool deleteDir(string $dirname)
 * @method static bool createDir(string $dirname, array $config = [])
 * @method static bool setVisibility(string $path, string $visibility)
 * @method static bool put(string $path, string $contents, array $config = [])
 * @method static bool putStream(string $path, resource $resource, array $config = [])
 * @method static string|false readAndDelete(string $path)
 * @method static string|false getPresignedUrl(string $path,string $expiration = "+20 minutes",array $getObjectOptions = [])
 */
class Storage extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'storage';
    }
}

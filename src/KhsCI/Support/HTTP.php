<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Curl\Curl;
use Exception;

class HTTP
{
    private static $curl;

    /**
     * @param string      $url
     * @param string|null $data
     * @param array       $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function post(string $url, string $data = null, array $header = [])
    {
        return self::getCurl()->post($url, $data, $header);
    }

    private static function getCurl()
    {
        if (!(self::$curl instanceof Curl)) {
            self::$curl = new Curl();
            self::$curl->setTimeout(5);
        }

        return self::$curl;
    }

    public static function close(): void
    {
        self::$curl = null;
    }

    /**
     * @param string      $url
     * @param string|null $data
     * @param array       $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function get(string $url, string $data = null, array $header = [])
    {
        return self::getCurl()->get($url, $data, $header);
    }

    /**
     * @param string $url
     * @param array  $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function delete(string $url, array $header = [])
    {
        return self::getCurl()->delete($url, $header);
    }
}

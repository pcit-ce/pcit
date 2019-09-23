<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

use Curl\Curl;
use Exception;

class HTTP
{
    private static $curl;

    private static $code;

    /**
     * @return mixed
     */
    public static function getCode()
    {
        return self::$code;
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
    public static function post(string $url, string $data = null, array $header = [])
    {
        $curl = self::getCurl();
        $result = $curl->post($url, $data, $header);
        self::$code = $curl->getCode();

        return $result;
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
     * @param int         $timeout
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function get(string $url, string $data = null, array $header = [], $timeout = 5)
    {
        $curl = self::getCurl();
        $source_timeout = $curl->timeout ?? 0;
        $curl->setTimeout($timeout);
        $output = $curl->get($url, $data, $header);
        self::$code = $curl->getCode();
        $curl->setTimeout($source_timeout);

        return $output;
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
        $curl = self::getCurl();
        $output = $curl->delete($url, $header);
        self::$code = $curl->getCode();

        return $output;
    }
}

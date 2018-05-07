<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Curl\Curl;

class HTTP
{
    /**
     * @param string      $url
     * @param string|null $data
     * @param array       $header
     *
     * @return mixed
     * @throws \Exception
     */
    public static function post(string $url, string $data = null, array $header = [])
    {
        $curl = new Curl();

        return $curl->post($url, $data, $header);
    }

    /**
     * @param string      $url
     * @param string|null $data
     * @param array       $header
     *
     * @return mixed
     * @throws \Exception
     */
    public static function get(string $url, string $data = null, array $header = [])
    {
        $curl = new Curl();

        return $curl->get($url, $data, $header);
    }

    /**
     * @param string $url
     * @param array  $header
     *
     * @return mixed
     * @throws \Exception
     */
    public static function delete(string $url, array $header = [])
    {
        $curl = new Curl();

        return $curl->delete($url, $header);
    }
}

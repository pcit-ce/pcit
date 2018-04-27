<?php

namespace KhsCI\Support;

use Curl\Curl;

class HTTP
{
    public static function post(string $url, string $data = null, array $header = null)
    {
        $curl = new Curl();

        return $curl->post($url, $data, $header);
    }

    public static function get(string $url, string $data = null, array $header = null)
    {
        $curl = new Curl();

        return $curl->get($url, $data, $header);
    }

    public static function delete(string $url, array $header = null)
    {
        $curl = new Curl();

        return $curl->delete($url, $header);
    }

}

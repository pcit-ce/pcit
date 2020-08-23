<?php

declare(strict_types=1);

namespace PCIT\Runner\RPC;

use Curl\Curl;

class Kernel
{
    const NAMESPACE = '';

    /** @var Curl */
    public static $curl;

    public static function __callStatic($name, $arguments)
    {
        $class = explode('\\', static::class)[3];

        $json_rpc = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => static::NAMESPACE.'\\'.$class.'::'.$name,
            'params' => $arguments,
        ];

        $result = static::getCurl()->post(
            config('app.rpc_host').'/rpc',
            json_encode($json_rpc),
            []
        );

        return json_decode($result, true)['result'] ?? null;
    }

    public static function getCurl(): Curl
    {
        if (!static::$curl) {
            $curl = new Curl();

            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);

            static::$curl = $curl;
        }

        return static::$curl;
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Runner\RPC;

use PCIT\Framework\Support\HttpClient;

class Kernel
{
    const NAMESPACE = '';

    public static function __callStatic($name, $arguments)
    {
        $class = explode('\\', static::class)[3];

        $json_rpc = [
            'jsonrpc' => 2,
            'id' => 1,
            'method' => static::NAMESPACE.'\\'.$class.'::'.$name,
            'params' => $arguments,
        ];

        $result = HttpClient::post(
            config('app.rpc_host').'/rpc',
            json_encode($json_rpc),
            []
        );

        return json_decode($result, true)['result'] ?? null;
    }
}

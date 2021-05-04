<?php

declare(strict_types=1);

namespace PCIT\Runner\RPC;

use App\RPC\Handler as RPCHandler;
use Curl\Curl;

class Kernel
{
    public const NAMESPACE = '';

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

        if (app()->environment('testing')) {
            $json_rpc_response = (new RPCHandler())->handle(json_encode($json_rpc));

            return $json_rpc_response['result'] ?? null;
        }

        $json_rpc_response = static::getCurl()->post(
            config('app.rpc_host').'/rpc',
            json_encode($json_rpc),
            ['Authorization' => 'token '.config('rpc.secret')]
        );

        if ($error = json_decode($json_rpc_response, true)['error'] ?? []) {
            \Log::emergency('call rpc meet error', $error);

            throw new \Exception();
        }

        return json_decode($json_rpc_response, true)['result'] ?? null;
    }

    public static function getCurl(): Curl
    {
        if (!static::$curl) {
            $curl = new Curl();

            $curl->setOpt(\CURLOPT_SSL_VERIFYPEER, 0);
            $curl->setOpt(\CURLOPT_SSL_VERIFYHOST, 0);

            static::$curl = $curl;
        }

        return static::$curl;
    }
}

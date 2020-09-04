<?php

declare(strict_types=1);

namespace App\RPC;

class Handler
{
    public function handle(string $json_rpc): array
    {
        $json_obj = json_decode($json_rpc);
        $class_and_method = $json_obj->method;
        $params = $json_obj->params;

        [$class, $method] = explode('::', $class_and_method);

        if ('\Cache' === $class) {
            $result = $class::$method(...$params);
        } else {
            $rf = new \ReflectionMethod(...explode('::', $class_and_method));
            $result = $rf->invokeArgs(null, $params);
        }

        $jsonrpc = '2.0';
        $id = $json_obj->id;

        $error = [
            'code' => '',
            'message' => '',
            'data' => '',
        ];

        return compact('jsonrpc', 'result', 'id');
    }
}

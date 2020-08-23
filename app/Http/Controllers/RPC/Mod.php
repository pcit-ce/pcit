<?php

namespace App\Http\Controllers\RPC;

use PCIT\Framework\Attributes\Route;

/**
 * @see http://wiki.geekdream.com/Specification/json-rpc_2.0.html
 */
class Mod
{
    @@Route('post','rpc')
    public function __invoke()
    {
        $content = \Request::getContent();

        $json_obj = json_decode($content);

        $class_and_method = $json_obj->method;
        $params = $json_obj->params;

        [$class, $method] = explode('::', $class_and_method);

        if ($class === '\Cache') {
            $result = $class::$method(...$params);
        } else {
            $rf = new \ReflectionMethod(...explode('::', $class_and_method));
            $result = $rf->invokeArgs(null, $params);
        }

        $jsonrpc = '2.0';
        $id = $json_obj->id;

        $error = [
            "code" => '',
            "message" => '',
            "data" => ""
        ];

        return compact('jsonrpc', 'result', 'id');
    }
}

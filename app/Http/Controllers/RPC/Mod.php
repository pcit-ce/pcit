<?php

namespace App\Http\Controllers\RPC;

class Mod {
    @@\Route('post','rpc')
    public function __invoke()
    {
        $content = \Request::getContent();

        $json_obj = json_decode($content);

        $class_and_method = $json_obj->method;
        $params = $json_obj->params;

        [$class,$method] = explode('::',$class_and_method);

        if($class === '\Cache'){
            $result = $class::$method(...$params);
        }else{
          $rf = new \ReflectionMethod(...explode('::',$class_and_method));
          $result = $rf->invokeArgs(null,$params);
        }

        return compact('result');
    }
}

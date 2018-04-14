<?php

namespace KhsCI\Support;

class Response
{
    public static function json(array $array)
    {
        header('content-type: application/json;charset=utf-8');
        echo json_encode($array);
    }

    public static function redirect($url)
    {
        header('Location:'.$url);
    }
}
<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Response
{
    public static function json(array $array): void
    {
        header('content-type: application/json;charset=utf-8');
        echo json_encode($array);
    }

    public static function redirect($url): void
    {
        header('Location:'.$url);
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Response
{
    const HTTP_CODE = [
        200,
        401,
        404,
        500,
    ];

    /**
     * @param array $array
     * @param float $time
     */
    public static function json(array $array, float $time): void
    {
        header('content-type: application/json;charset=utf-8');
        $time = microtime(true) - $time;
        header("X-Runtime-rack: $time");
        $code = $array['code'] ?? 200;

        if (in_array($code, self::HTTP_CODE, true)) {
            http_response_code($code);
        }

        echo json_encode($array);
    }

    /**
     * @param string $url
     */
    public static function redirect(string $url): void
    {
        header('Location:'.$url);
        http_response_code(301);
        exit;
    }
}

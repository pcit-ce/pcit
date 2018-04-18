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
     */
    public static function json(array $array): void
    {
        header('content-type: application/json;charset=utf-8');

        $code = $array['code'] ?? 200;

        if (in_array($code, self::HTTP_CODE)) {
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

    /**
     * @param $data
     */
    public static function return200(array $data)
    {
        $code = [
            'code' => 200
        ];

        self::json(array_merge($code, $data));
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Support;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    const HTTP_CODE = [
        200,
        304,
        401,
        402,
        403,
        404,
        422,
        500,
    ];

    /**
     * @param array $array
     * @param float $startedAt
     *
     * @return false|string
     */
    public static function json(array $array, float $startedAt)
    {
        header('content-type: application/json;charset=utf-8');
        $time = microtime(true) - $startedAt;
        header("X-Runtime-rack: $time");
        $code = $array['code'] ?? 200;

        if (\in_array($code, self::HTTP_CODE, true)) {
            http_response_code($code);

            unset($array['code']);
        }

        return new self(json_encode($array), $code);
    }

    /**
     * @param string $url
     */
    public static function redirect(string $url): void
    {
        header('Location: '.$url);
        exit;
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
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
     * @param array|string $content
     *
     * @return false|string
     */
    public static function json($content, $json = false)
    {
        if (\defined('PCIT_START')) {
            $time = microtime(true) - PCIT_START;
        }

        if (!$json) {
            $code = $content['code'] ?? 200;
            unset($content['code']);
        }

        return new JsonResponse($content, $code ?? 200, ['X-Runtime-rack' => $time], $json);
    }

    public static function redirect(string $url): void
    {
        header('Location: '.$url);
        exit;
    }
}

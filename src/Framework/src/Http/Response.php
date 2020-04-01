<?php

declare(strict_types=1);

namespace PCIT\Framework\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Response extends BaseResponse
{
    const HTTP_CODE = [
        200,
        204,

        304,

        401,
        402,
        403,
        404,
        422,

        500,
    ];

    /**
     * @param mixed $content The response content, see setContent()
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     */
    public function make($content = '', int $status = 200, array $headers = [])
    {
        $status = \in_array($status, self::HTTP_CODE) ? $status : 500;

        $response = app()->make('response')->setContent($content)
        ->setStatusCode($status)->setHeaders($headers);

        return $response;
    }

    /**
     * @param array|string $content
     *
     * @return false|string
     */
    public function json($content, $json = false)
    {
        if (\defined('PCIT_START')) {
            $time = microtime(true) - PCIT_START;
        }

        if (!$json) {
            $code = $content['code'] ?? 200;
            $code = \in_array($code, self::HTTP_CODE) ? $code : 500;
            unset($content['code']);
        }

        return new JsonResponse($content, $code ?? 200, ['X-Runtime-rack' => $time], $json);
    }

    public function redirect(string $url): void
    {
        header('Location: '.$url);
        exit;
    }

    /**
     * @param array $headers An array of response headers
     */
    public function setHeaders(array $headers = [])
    {
        $response = app('response');
        $response->headers = new ResponseHeaderBag($headers);

        return $response;
    }
}

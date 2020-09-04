<?php

declare(strict_types=1);

namespace PCIT\Framework\Http;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @method ResponseHeaderBag headers()
 */
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

        return app()->make('response')->setContent($content)
            ->setStatusCode($status)->setHeaders($headers);
    }

    /**
     * @param array|string $content
     */
    public function json($content, bool $json = false): BaseResponse
    {
        if (\defined('PCIT_START')) {
            $time = microtime(true) - PCIT_START;
        }

        if (!$json) {
            $code = $content['code'] ?? 200;
            $code = \in_array($code, self::HTTP_CODE) ? $code : 500;
            unset($content['code']);
        }

        $this->setHeaders(['X-Runtime-rack' => $time]);

        return new JsonResponse(
            $content,
            $code ?? 200,
            $this->headers()->all(),
            $json
        );
    }

    public function noContent($status = 204, array $headers = [])
    {
        return $this->make('', $status, $headers);
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
        $this->headers->add($headers);

        return $this;
    }

    public function stream(\Closure $callback, int $status = 200, array $headers = [])
    {
        return new StreamedResponse($callback, $status, $headers);
    }

    public function __call(string $method, array $args)
    {
        return $this->$method;
    }

    /**
     * @param \SplFileInfo|string $file
     */
    public function download(
        $file,
        ?string $name = null,
        array $headers = [],
        string $disposition = HeaderUtils::DISPOSITION_ATTACHMENT
    ): BinaryFileResponse {
        $response = new BinaryFileResponse($file, 200, $headers, true, $disposition);

        if (null !== $name) {
            return $response->setContentDisposition($disposition, $name);
        }

        return $response;
    }

    /**
     * @param \SplFileInfo|string $file
     */
    public function file($file, array $headers = []): BinaryFileResponse
    {
        return new BinaryFileResponse($file, 200, $headers);
    }
}

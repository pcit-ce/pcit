<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation\Exceptions;

abstract class Handler extends \Exception
{
    /**
     * 用于记录异常或将其发送到外部服务
     */
    public function report(\Throwable $exception): void
    {
        $jsonMessage = $this->generateJsonMessage($exception, true);

        $jsonMessage['details']['trace'] = $jsonMessage['details']['trace'][0];

        \Log::emergency('Response error', $jsonMessage);
    }

    /**
     * 负责将给定的异常转换成发送给浏览器的 HTTP 响应.
     *
     * @param mixed $request
     */
    public function render($request, \Throwable $exception)
    {
        $debug = config('app.debug');

        return \Response::json($this->generateJsonMessage($exception, $debug));
    }

    public function generateJsonMessage(\Throwable $exception, bool $debug = false): array
    {
        $errDetails['trace'] = $debug ? $exception->getTrace()
            : 'please enable debug mode see more';

        return array_filter([
            'code' => $exception->getCode() ?: 500,
            'message' => $exception->getMessage() ?: 'ERROR',
            'documentation_url' => 'https://github.com/pcit-ce/pcit/tree/master/docs/api',
            'file' => $debug ? $exception->getFile() : null,
            'line' => $debug ? $exception->getLine() : null,
            'details' => $debug ? $errDetails : null,
        ]);
    }
}

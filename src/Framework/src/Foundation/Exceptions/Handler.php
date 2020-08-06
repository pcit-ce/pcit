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
    }

    /**
     * 负责将给定的异常转换成发送给浏览器的 HTTP 响应.
     *
     * @param mixed $request
     */
    public function render($request, \Throwable $exception)
    {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Framework\Routing\Exceptions;

/**
 * 路由请求成功
 */
class SuccessHandleRouteException extends \Exception
{
    public $code = 200;
}

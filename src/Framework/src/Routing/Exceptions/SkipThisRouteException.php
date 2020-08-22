<?php

declare(strict_types=1);

namespace PCIT\Framework\Routing\Exceptions;

/**
 * 跳过此条路由规则，继续匹配下一个路由.
 */
class SkipThisRouteException extends \Exception
{
}

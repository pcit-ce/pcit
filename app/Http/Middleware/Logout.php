<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use PCIT\Framework\Http\Request;
use PCIT\Framework\Routing\Exceptions\SkipThisRouteException;

class Logout
{
    public function handle(Request $request, \Closure $next, $guard = null)
    {
        $pathinfo = $request->getPathInfo();

        if ('logout' === explode('/', $pathinfo)[2]) {
            throw new SkipThisRouteException();
        }

        return $next($request);
    }
}

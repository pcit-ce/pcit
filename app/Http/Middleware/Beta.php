<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use PCIT\Framework\Http\Request;

class Beta
{
    public $message;

    public function __construct(Request $request, \App\Beta\Beta $beta)
    {
        $this->message = $beta->message;
    }

    /**
     * @param null|string $guard
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, $guard = null)
    {
        return $this->message;

        return $next($request);
        // $response = $next($request);

        // 执行动作

        // var_dump($response);

        // return $response;
    }
}

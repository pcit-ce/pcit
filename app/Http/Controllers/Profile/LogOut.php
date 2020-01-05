<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use PCIT\Framework\Support\Session;
use PCIT\Support\Env;

/**
 * 注销
 */
class LogOut
{
    public function __invoke(...$args): void
    {
        list($git_type) = $args;

        Session::pull($git_type.'.access_token');

        setcookie(
            $git_type.'_api_token',
            '',
            time() - 3600,
            '/',
            env('CI_SESSION_DOMAIN', 'ci.khs1994.com'), true
        );

        \Response::redirect(env('CI_HOST'));
    }
}

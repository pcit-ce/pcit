<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use PCIT\Support\Env;
use PCIT\Support\Response;
use PCIT\Support\Session;

/**
 * 注销
 */
class LogOut
{
    public function __invoke(...$args): void
    {
        list($git_type) = $args;

        Session::pull($git_type.'.access_token');

        Response::redirect(env('CI_HOST'));
    }
}

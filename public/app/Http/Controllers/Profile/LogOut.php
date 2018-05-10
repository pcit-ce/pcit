<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class LogOut
{
    public function __invoke(...$args): void
    {
        list($git_type) = $args;

        Session::pull($git_type.'.access_token');

        Response::redirect(Env::get('CI_HOST'));
    }
}

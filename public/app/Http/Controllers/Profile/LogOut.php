<?php

namespace App\Http\Controllers\Profile;

use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class LogOut
{
    public function __invoke(...$args)
    {
        list($git_type) = $args;

        Session::pull($git_type.'.access_token');

        Response::redirect(Env::get('CI_HOST'));
    }
}

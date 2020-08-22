<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

/**
 * 注销
 */
class LogOut
{
    @@\Route('get', '{git_type}/logout')
    public function __invoke($git_type): void
    {
        \Session::pull($git_type.'.access_token');

        setcookie(
            $git_type.'_api_token',
            '',
            time() - 3600,
            '/',
            config('session.domain')
        );

        \Response::redirect(config('app.host'));
    }
}

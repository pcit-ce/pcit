<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use KhsCI\KhsCI;
use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class GitHubAppController
{
    private static $git_type = 'github_app';

    public function __invoke(...$args)
    {
        $git_type = static::$git_type;

        $username_from_web = $args[0];

        $access_token = Session::get(static::$git_type.'.access_token');

        $username = Session::get(static::$git_type.'.github');

        if (null === $username or null === $access_token) {
            Response::redirect(Env::get('CI_HOST').'/login');
        }

        if ($username_from_web !== $username) {
            Response::redirect('/profile/'.$git_type.'/'.$username);
        }

        $access_token = Session::get(static::$git_type.'.access_token');

        $khsci = new KhsCI(['github_app_access_token' => $access_token]);

        return json_decode($khsci->github_apps_installations->listRepositoriesAccessible(162542), true);
    }
}

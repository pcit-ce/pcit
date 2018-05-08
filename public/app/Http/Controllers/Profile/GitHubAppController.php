<?php

namespace App\Http\Controllers\Profile;

use KhsCI\KhsCI;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class GitHubAppController
{
    private static $git_type = 'github_app';

    public function __invoke()
    {
        $access_token = Session::get(static::$git_type.'.access_token');

        $khsci = new KhsCI(['github_app_access_token' => $access_token]);

        return json_decode($khsci->github_apps_installations->list(), true);
    }
}

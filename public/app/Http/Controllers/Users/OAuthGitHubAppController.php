<?php

namespace App\Http\Controllers\Users;


use KhsCI\KhsCI;

class OAuthGitHubAppController extends OAuthGitHubController
{
    use OAuthTrait;

    protected static $oauth;

    protected static $git_type = 'github_app';

    public function __construct()
    {
        parent::__construct();

        $khsci = new KhsCI();

        static::$oauth = $khsci->oauth_github_app;
    }
}

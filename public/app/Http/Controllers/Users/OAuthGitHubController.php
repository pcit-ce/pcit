<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\{
    Coding,
    Gitee,
    GitHub,
    GitHubApp
};
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class OAuthGitHubController
{
    /**
     * @var GitHubApp|GitHub|Coding|Gitee
     */
    protected static $oauth;

    protected static $git_type = 'github';

    use OAuthTrait;

    public function __construct()
    {
        $khsci = new KhsCI();

        $method = 'oauth_'.static::$git_type;

        static::$oauth = $khsci->$method;
    }

    public function getLoginUrl(): void
    {
        $state = session_create_id();

        Session::put(static::$git_type.'.state', $state);

        $url = static::$oauth->getLoginUrl($state);

        Response::redirect($url);
    }

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $state = Session::pull(static::$git_type.'.state');

        $this->getAccessTokenCommon($state);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\Coding;
use KhsCI\Service\OAuth\Gitee;
use KhsCI\Service\OAuth\GitHub;
use KhsCI\Service\OAuth\GitHubApp;
use KhsCI\Support\Env;
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
        $git_type = static::$git_type;

        if (Session::get($git_type.'.access_token')) {
            $username_from_session = Session::get($git_type.'.username');

            Response::redirect(implode('/', ['/profile', $git_type, $username_from_session]));
        }

        $state = session_create_id();

        Session::put($git_type.'.state', $state);

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

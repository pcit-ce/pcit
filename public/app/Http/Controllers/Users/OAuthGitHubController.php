<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class OAuthGitHubController
{
    private $oauth;

    use OAuthTrait;

    public function __construct()
    {
        $khsci = new KhsCI();

        $this->oauth = $khsci->oauth_github;
    }

    public function getLoginUrl(): void
    {
        $state = session_create_id();

        Session::put('github.state', $state);

        $url = $this->oauth->getLoginUrl($state);

        Response::redirect($url);
    }

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $state = Session::pull('github.state');

        $this->getAccessTokenCommon('gitHub', $state);
    }
}

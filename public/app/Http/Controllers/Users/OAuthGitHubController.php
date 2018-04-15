<?php

declare(strict_types=1);

namespace App\Http\controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\GitHub;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class OAuthGitHubController
{
    private $ci;

    use OAuthTrait;

    public function __construct()
    {
        $this->ci = new KhsCI();
    }

    public function getLoginUrl(): void
    {
        $state = session_create_id();

        Session::put('github.state', $state);

        $url = $this->ci->OAuthGitHub->getLoginUrl($state);

        Response::redirect($url);
    }

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $state = Session::get('github.state');

        $this->getAccessTokenCommon('gitHub', $state);
    }
}

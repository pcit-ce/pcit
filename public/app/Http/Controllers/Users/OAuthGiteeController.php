<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Response;

class OAuthGiteeController
{
    use OAuthTrait;

    private $oauth;

    public function __construct()
    {
        $khsci = new KhsCI();

        $this->oauth = $khsci->oauth_gitee;
    }

    public function getLoginUrl(): void
    {
        $url = $this->oauth->getLoginUrl(null);

        Response::redirect($url);
    }

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $this->getAccessTokenCommon('gitee', null);
    }
}

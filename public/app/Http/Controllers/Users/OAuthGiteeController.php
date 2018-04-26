<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Response;

class OAuthGiteeController
{
    use OAuthTrait;

    private $ci;

    public function __construct()
    {
        $this->ci = new KhsCI();
    }

    public function getLoginUrl(): void
    {
        $url = $this->ci->OAuthGitee->getLoginUrl(null);

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

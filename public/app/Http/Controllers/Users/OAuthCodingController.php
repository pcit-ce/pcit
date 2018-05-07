<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Response;

class OAuthCodingController
{
    use OAuthTrait;

    private $oauth;

    public function __construct()
    {
        $khsci = new KhsCI();

        $this->oauth = $khsci->oauth_coding;
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
        $this->getAccessTokenCommon('coding', null);
    }
}

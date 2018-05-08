<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Response;

class OAuthCodingController
{
    use OAuthTrait;

    protected static $oauth;

    protected static $git_type = 'coding';

    public function __construct()
    {
        $khsci = new KhsCI();

        static::$oauth = $khsci->oauth_coding;
    }

    public function getLoginUrl(): void
    {
        $url = static::$oauth->getLoginUrl(null);

        Response::redirect($url);
    }

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $this->getAccessTokenCommon(null);
    }
}

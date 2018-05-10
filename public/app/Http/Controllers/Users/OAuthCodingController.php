<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\OAuth\Coding;
use KhsCI\Support\Response;

class OAuthCodingController
{
    use OAuthTrait;

    /**
     * @var Coding
     */
    protected static $oauth;

    protected static $git_type = 'coding';

    /**
     * OAuthCodingController constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $khsci = new KhsCI();

        static::$oauth = $khsci->oauth_coding;
    }

    /**
     * OAuth 第一步获取登录 URL.
     */
    public function getLoginUrl(): void
    {
        $url = static::$oauth->getLoginUrl(null);

        Response::redirect($url);
    }

    /**
     * OAuth 第二步在回调地址发起 POST 请求，返回 Access_Token.
     *
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $this->getAccessTokenCommon(null);
    }
}

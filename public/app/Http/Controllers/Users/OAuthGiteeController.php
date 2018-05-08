<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;
use KhsCI\KhsCI;

class OAuthGiteeController extends OAuthGitHubController
{
    use OAuthTrait;

    protected static $oauth;

    protected static $git_type = 'github';

    public function __construct()
    {
        $khsci = new KhsCI();

        static::$oauth = $khsci->oauth_gitee;
    }

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $this->getAccessTokenCommon(null);
    }
}

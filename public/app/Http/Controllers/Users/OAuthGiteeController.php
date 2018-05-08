<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Exception;

class OAuthGiteeController extends OAuthGitHubController
{
    use OAuthTrait;

    protected static $oauth;

    protected static $git_type = 'gitee';

    /**
     * @throws Exception
     */
    public function getAccessToken(): void
    {
        $this->getAccessTokenCommon(null);
    }
}

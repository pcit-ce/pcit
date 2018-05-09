<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

class OAuthGitHubAppController extends OAuthGitHubController
{
    use OAuthTrait;

    protected static $oauth;

    protected static $git_type = 'github_app';
}

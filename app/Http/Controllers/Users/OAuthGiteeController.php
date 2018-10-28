<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

class OAuthGiteeController extends OAuthKernel
{
    protected static $oauth;

    protected static $git_type = 'gitee';
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

class OAuthGitHubController extends OAuthKernel
{
    protected static $git_type = 'github';

    protected $state = true;
}

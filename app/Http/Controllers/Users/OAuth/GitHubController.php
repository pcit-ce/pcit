<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\OAuth;

class GitHubController extends Kernel
{
    protected static $git_type = 'github';

    protected $state = true;
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

class GiteeController extends GitHubController
{
    protected $git_type = 'gitee';
}

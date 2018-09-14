<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

class GitHubController extends Kernel
{
    protected static $git_type = 'github';
}

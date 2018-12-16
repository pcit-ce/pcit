<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\OAuth;

class GiteeController extends Kernel
{
    protected static $oauth;

    protected static $git_type = 'gitee';
}

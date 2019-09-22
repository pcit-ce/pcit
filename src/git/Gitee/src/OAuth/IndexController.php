<?php

declare(strict_types=1);

namespace PCIT\Gitee\OAuth;

use App\Http\Controllers\OAuth\Kernel;

class IndexController extends Kernel
{
    protected static $oauth;

    protected static $git_type = 'gitee';
}

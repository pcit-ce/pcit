<?php

declare(strict_types=1);

namespace PCIT\Coding\OAuth;

use App\Http\Controllers\OAuth\Kernel;

class IndexController extends Kernel
{
    protected static $oauth;

    protected static $git_type = 'coding';
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\OAuth\Coding;

use App\Http\Controllers\OAuth\Kernel;

class IndexController extends Kernel
{
    protected static $oauth;

    protected static $git_type = 'coding';
}

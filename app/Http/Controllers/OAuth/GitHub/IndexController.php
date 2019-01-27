<?php

declare(strict_types=1);

namespace App\Http\Controllers\OAuth\GitHub;

use App\Http\Controllers\OAuth\Kernel;

class IndexController extends Kernel
{
    protected static $git_type = 'github';

    protected $state = true;
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\OAuth;

use App\Http\Controllers\OAuth\Kernel;

class IndexController extends Kernel
{
    protected static $git_type = 'github';

    protected $state = true;
}

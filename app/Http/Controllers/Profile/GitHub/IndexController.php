<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile\GitHub;

use App\Http\Controllers\Profile\Kernel;

class IndexController extends Kernel
{
    protected $git_type = 'github';
}

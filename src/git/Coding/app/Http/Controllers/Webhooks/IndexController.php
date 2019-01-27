<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks\Coding;

use App\Http\Controllers\Webhooks\Kernel;

class IndexController extends Kernel
{
    protected static $git_type = 'coding';
}

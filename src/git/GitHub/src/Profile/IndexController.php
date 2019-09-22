<?php

declare(strict_types=1);

namespace PCIT\GitHub\Profile;

use App\Http\Controllers\Profile\Kernel;

class IndexController extends Kernel
{
    protected $git_type = 'github';
}

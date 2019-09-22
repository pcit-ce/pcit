<?php

declare(strict_types=1);

namespace PCIT\Gogs\Profile;

use App\Http\Controllers\Profile\Kernel;

class IndexController extends Kernel
{
    protected $git_type = 'gogs';
}

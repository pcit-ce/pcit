<?php

declare(strict_types=1);

namespace PCIT\Gogs\WebhooksServer;

use App\Http\Controllers\WebhooksServer\Kernel;

class IndexController extends Kernel
{
    protected static $git_type = 'gogs';
}

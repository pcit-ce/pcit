<?php

declare(strict_types=1);

namespace PCIT\Gitee\WebhooksServer;

use App\Http\Controllers\WebhooksServer\Kernel;

class GiteeController extends Kernel
{
    protected static $git_type = 'gitee';
}
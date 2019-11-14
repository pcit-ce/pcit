<?php

declare(strict_types=1);

namespace PCIT\Coding\Webhooks\Server;

use App\Http\Controllers\WebhooksServer\Kernel;

class IndexController extends Kernel
{
    protected static $git_type = 'coding';
}

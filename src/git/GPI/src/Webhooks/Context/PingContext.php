<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class PingContext extends Context
{
    public $zen;

    public $hook_id;

    public $hook;

    use ContextTrait;
}

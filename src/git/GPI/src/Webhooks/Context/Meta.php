<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class Meta extends Context
{
    /*@var "deleted" */
    public string $action;

    public int $hook_id;

    public $hook;

    use ContextTrait;
}

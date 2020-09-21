<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property "deleted" $action
 */
class Meta extends Context
{
    public string $action;

    public int $hook_id;

    public $hook;

    use ContextTrait;
}

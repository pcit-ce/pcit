<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context\Components\Milestone;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class MilestoneContext extends Context
{
    public Milestone $milestone;

    public $changes;

    use ContextTrait;
}

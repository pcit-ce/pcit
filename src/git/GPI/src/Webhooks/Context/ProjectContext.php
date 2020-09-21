<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class ProjectContext extends Context
{
    /**
     * The changes to the project if the action was `edited`.
     */
    public $changes;

    public $project;

    use ContextTrait;
}

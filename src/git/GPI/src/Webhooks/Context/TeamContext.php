<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Team;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class TeamContext extends Context
{
    public Team $team;

    /**
     * The changes to the team if the action was `edited`.
     */
    public $changes;

    use ContextTrait;
}

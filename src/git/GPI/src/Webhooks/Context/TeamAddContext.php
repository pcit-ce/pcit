<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Team;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class TeamAddContext extends Context
{
    public Team $team;

    use ContextTrait;
}

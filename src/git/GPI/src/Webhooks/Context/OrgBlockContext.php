<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\User;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to people being blocked in an organization.
 */
class OrgBlockContext extends Context
{
    public User $blocked_user;

    use ContextTrait;
}

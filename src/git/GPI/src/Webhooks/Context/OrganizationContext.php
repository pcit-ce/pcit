<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to an organization and its members.
 */
class OrganizationContext extends Context
{
    /**
     * The invitation for the user or email if the action is `member_invited`.
     */
    public $invitation;

    /**
     * The membership between the user and the organization.
     * Not present when the action is `member_invited`.
     */
    public $membership;

    use ContextTrait;
}

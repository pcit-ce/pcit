<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Team;
use PCIT\GPI\Webhooks\Context\Components\User\User;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to team membership.
 *
 * @property "added"|"removed" $action
 */
class MembershipContext extends Context
{
    /**
     * The scope of the membership. Currently, can only be team.
     */
    public string $scope;

    public User $member;

    public Team $team;

    use ContextTrait;
}

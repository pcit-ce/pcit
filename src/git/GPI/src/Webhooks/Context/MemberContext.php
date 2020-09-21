<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\User;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * Activity related to repository collaborators.
 *
 * @property "added"|"removed"|"edited" $action
 * @property int                        $rid
 * @property string                     $repo_full_name
 * @property int                        $member_uid
 * @property string                     $member_username
 * @property string                     $member_pic
 */
class MemberContext extends Context
{
    public User $member;

    /**
     * The changes to the collaborator permissions if the action was `edited`.
     */
    public $changes;

    use ContextTrait;
}

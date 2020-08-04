<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;

/**
 * @property "added"|"removed"|"edited" $action
 * @property int                        $installation_id
 * @property int                        $rid
 * @property string                     $repo_full_name
 * @property int                        $member_uid
 * @property string                     $member_username
 * @property string                     $member_pic
 * @property Owner                      $owner
 */
class MemberContext extends Context
{
}

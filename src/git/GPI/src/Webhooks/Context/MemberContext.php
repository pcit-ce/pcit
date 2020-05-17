<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property string  $action
 * @property string  $installation_id
 * @property int     $rid
 * @property string  $repo_full_name
 * @property int     $member_uid
 * @property string  $member_username
 * @property string  $member_pic
 * @property Account $account
 */
class MemberContext extends Context
{
}

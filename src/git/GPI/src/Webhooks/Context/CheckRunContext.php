<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property int                                                    $installation_id
 * @property int                                                    $rid
 * @property string                                                 $repo_full_name
 * @property "created"|"completed"|"rerequested"|"requested_action" $action
 * @property string                                                 $branch
 * @property string                                                 $commit_id
 * @property int                                                    $check_suite_id
 * @property int                                                    $check_run_id
 * @property int                                                    $external_id
 * @property Account                                                $account
 */
class CheckRunContext extends Context
{
}

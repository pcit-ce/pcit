<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property int                                                                                                                                           $installation_id
 * @property int                                                                                                                                           $rid
 * @property string                                                                                                                                        $repo_full_name
 * @property int                                                                                                                                           $event_time
 * @property string                                                                                                                                        $commit_message
 * @property string                                                                                                                                        $commit_id
 * @property string                                                                                                                                        $committer_username
 * @property string                                                                                                                                        $committer_uid
 * @property string                                                                                                                                        $pull_request_number
 * @property string                                                                                                                                        $branch
 * @property string                                                                                                                                        $internal
 * @property string                                                                                                                                        $pull_request_source
 * @property Account                                                                                                                                       $account
 * @property "assigned"|"unassigned"|"review_requested"|"review_request_removed"|"labeled"|"unlabeled"|"opened"|"synchronize"|"edited"|"closed"|"reopened" $action
 */
class PullRequestContext extends Context
{
}

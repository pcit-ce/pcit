<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Context;

/**
 * @property string                                                                                                                            $installation_id
 * @property string                                                                                                                            $rid
 * @property string                                                                                                                            $repo_full_name
 * @property string                                                                                                                            $issue_id
 * @property string                                                                                                                            $issue_number
 * @property string                                                                                                                            $title
 * @property string                                                                                                                            $body
 * @property string                                                                                                                            $sender_uid
 * @property string                                                                                                                            $sender_username
 * @property string                                                                                                                            $sender_pic
 * @property string                                                                                                                            $state
 * @property string                                                                                                                            $locked
 * @property string                                                                                                                            $assignees
 * @property string                                                                                                                            $labels
 * @property string                                                                                                                            $created_at
 * @property string                                                                                                                            $updated_at
 * @property string                                                                                                                            $closed_at
 * @property Account                                                                                                                           $account
 * @property "assigned" | "unassigned" | "labeled" | "unlabeled" | "opened" | "closed" | "reopened" | "edited" | "milestoned" | "demilestoned" $action
 */
class IssuesContext extends Context
{
}

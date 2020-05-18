<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property int                                                                                                             $installation_id
 * @property int                                                                                                             $rid
 * @property string                                                                                                          $repo_full_name
 * @property int                                                                                                             $issue_id
 * @property int                                                                                                             $issue_number
 * @property string                                                                                                          $title
 * @property string                                                                                                          $body
 * @property int                                                                                                             $sender_uid
 * @property string                                                                                                          $sender_username
 * @property string                                                                                                          $sender_pic
 * @property string                                                                                                          $state
 * @property string                                                                                                          $locked
 * @property string                                                                                                          $assignees
 * @property string                                                                                                          $labels
 * @property int                                                                                                             $created_at
 * @property int                                                                                                             $updated_at
 * @property int                                                                                                             $closed_at
 * @property Account                                                                                                         $account
 * @property "assigned"|"unassigned"|"labeled"|"unlabeled"|"opened"|"closed"|"reopened"|"edited"|"milestoned"|"demilestoned" $action
 */
class IssuesContext extends Context
{
}

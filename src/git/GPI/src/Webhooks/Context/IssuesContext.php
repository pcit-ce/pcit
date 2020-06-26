<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Owner;

/**
 * @property int                                                                                                                                                                             $installation_id
 * @property int                                                                                                                                                                             $rid
 * @property string                                                                                                                                                                          $repo_full_name
 * @property int                                                                                                                                                                             $issue_id
 * @property int|string                                                                                                                                                                      $issue_number
 * @property string                                                                                                                                                                          $title
 * @property string                                                                                                                                                                          $body
 * @property int                                                                                                                                                                             $sender_uid
 * @property string                                                                                                                                                                          $sender_username
 * @property string                                                                                                                                                                          $sender_pic
 * @property string                                                                                                                                                                          $state
 * @property string                                                                                                                                                                          $locked
 * @property string                                                                                                                                                                          $assignees
 * @property string                                                                                                                                                                          $labels
 * @property int                                                                                                                                                                             $created_at
 * @property int                                                                                                                                                                             $updated_at
 * @property int                                                                                                                                                                             $closed_at
 * @property Owner                                                                                                                                                                           $owner
 * @property "assigned"|"unassigned"|"labeled"|"unlabeled"|"opened"|"closed"|"reopened"|"edited"|"milestoned"|"demilestoned"|"deleted"|"pinned"|"unpinned"|"locked"|"unlocked"|"transferred" $action
 */
class IssuesContext extends Context
{
}

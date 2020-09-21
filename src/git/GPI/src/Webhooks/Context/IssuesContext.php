<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Issue;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
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
 * @property "assigned"|"unassigned"|"labeled"|"unlabeled"|"opened"|"closed"|"reopened"|"edited"|"milestoned"|"demilestoned"|"deleted"|"pinned"|"unpinned"|"locked"|"unlocked"|"transferred" $action
 */
class IssuesContext extends Context
{
    public Issue $issue;

    /**
     * The changes to the issue if the action was `edited`.
     */
    public $changes;

    /**
     * The optional user who was assigned or unassigned from the issue.
     *
     * \PCIT\GPI\Webhooks\Context\Components\Assignee
     */
    public $assignee;

    /**
     * The optional label that was added or removed from the issue.
     *
     * \PCIT\GPI\Webhooks\Context\Components\Label
     */
    public $label;

    use ContextTrait;
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\PullRequest;
use PCIT\GPI\Webhooks\Context\Components\PullRequest\Base as PullRequestBase;
use PCIT\GPI\Webhooks\Context\Components\PullRequest\Head as PullRequestHead;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property "assigned"|"unassigned"|"review_requested"|"review_request_removed"|"ready_for_review"|"labeled"|"unlabeled"|"opened"|"synchronize"|"edited"|"closed"|"reopened"|"locked"|"unlocked" $action
 * @property int                                                                                                                                                                                  $rid
 * @property string                                                                                                                                                                               $repo_full_name
 * @property int                                                                                                                                                                                  $event_time
 * @property string                                                                                                                                                                               $commit_message
 * @property string                                                                                                                                                                               $commit_id
 * @property string                                                                                                                                                                               $committer_username
 * @property string                                                                                                                                                                               $committer_uid
 * @property string                                                                                                                                                                               $pull_request_number
 * @property string                                                                                                                                                                               $branch
 * @property 0|1                                                                                                                                                                                  $internal
 * @property PullRequestHead                                                                                                                                                                      $pullRequestHead
 * @property PullRequestBase                                                                                                                                                                      $pullRequestBase
 * @property string                                                                                                                                                                               $pull_request_source
 */
class PullRequestContext extends Context
{
    public int $number;

    /**
     * The changes to the comment if the action was `edited`.
     */
    public $changes;

    public PullRequest $pull_request;

    use ContextTrait;
}

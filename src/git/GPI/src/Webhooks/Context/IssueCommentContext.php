<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Issue;
use PCIT\GPI\Webhooks\Context\Components\IssueComment;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property "created"|"edited"|"deleted" $action
 * @property int                          $rid
 * @property string                       $repo_full_name
 * @property string                       $sender_username
 * @property int                          $sender_uid
 * @property string                       $sender_pic
 * @property int                          $issue_id
 * @property int|string                   $issue_number
 * @property string                       $comment_id
 * @property string                       $body
 * @property int                          $created_at
 * @property int                          $updated_at
 * @property bool                         $is_pull_request
 */
class IssueCommentContext extends Context
{
    public Issue $issue;

    public IssueComment $comment;

    /**
     * The changes to the comment if the action was `edited`.
     */
    public $changes;

    use ContextTrait;
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\PullRequest;
use PCIT\GPI\Webhooks\Context\Components\PullRequestComment;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class PullRequestReviewCommentContext extends Context
{
    /**
     * The changes to the comment if the action was `edited`.
     */
    public $changes;

    public PullRequest $pull_request;

    public PullRequestComment $comment;

    use ContextTrait;
}

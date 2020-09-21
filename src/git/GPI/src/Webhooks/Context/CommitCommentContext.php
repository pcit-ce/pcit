<?php

declare(strict_types=1);

namespace pcit\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context\Components\CommitComment;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class CommitCommentContext
{
    /** @var "created" */
    public $action;

    public CommitComment $comment;

    use ContextTrait;
}

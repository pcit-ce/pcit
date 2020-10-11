<?php

declare(strict_types=1);

namespace pcit\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\CommitComment;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property "created" $action
 */
class CommitCommentContext extends Context
{
    public CommitComment $comment;

    use ContextTrait;
}

<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\Issue;

use PCIT\GitHub\Webhooks\Parser\IssueCommentContext;

/**
 * Triggered when an issue comment is `created`, `edited`, or `deleted`.
 */
interface CommentInterface
{
    public function handle(IssueCommentContext $context);
}

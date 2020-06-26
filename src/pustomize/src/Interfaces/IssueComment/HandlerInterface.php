<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\IssueComment;

use PCIT\GPI\Webhooks\Context\IssueCommentContext;

interface HandlerInterface
{
    public function handle(IssueCommentContext $context);
}

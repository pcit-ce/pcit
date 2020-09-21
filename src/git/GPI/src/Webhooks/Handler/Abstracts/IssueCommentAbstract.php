<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Handler\Handler;
use PCIT\GPI\Webhooks\Handler\Interfaces\IssueCommentInterface;

abstract class IssueCommentAbstract extends Handler implements IssueCommentInterface
{
    public function pustomize(IssueCommentContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('IssueComment', $context);
    }
}

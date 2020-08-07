<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Handler\Interfaces\IssueCommentInterface;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class IssueCommentAbstract implements IssueCommentInterface
{
    public function pustomize(IssueCommentContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('IssueComment', $context);
    }
}

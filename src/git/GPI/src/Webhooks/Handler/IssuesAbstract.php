<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Issues;

use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class IssuesAbstract
{
    public function triggerIssuesPustomize(IssuesContext $context): void
    {
        $class = 'PCIT\\Pustomize\\Issue\\Basic';

        (new PustomizeHandler())->handle($class, $context);
    }

    public function triggerIssueCommentPustomize(IssueCommentContext $context): void
    {
        $class = 'PCIT\\Pustomize\\Issue\\Comment';

        (new PustomizeHandler())->handle($class, $context);
    }
}

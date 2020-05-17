<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use PCIT\GPI\Webhooks\Context\PullRequestContext;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class PullRequestAbstract implements PullRequestInterface
{
    public function triggerPullRequestPustomize(PullRequestContext $context): void
    {
        $class = 'PCIT\\Pustomize\\PullRequest\\Basic';

        (new PustomizeHandler())->handle($class, $context);
    }
}

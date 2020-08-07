<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\PullRequestContext;
use PCIT\GPI\Webhooks\Handler\Interfaces\PullRequestInterface;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class PullRequestAbstract implements PullRequestInterface
{
    public function pustomize(PullRequestContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        (new PustomizeHandler())->handle('PullRequest', $context);
    }
}

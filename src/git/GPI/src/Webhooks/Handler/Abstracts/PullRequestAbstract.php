<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\PullRequestContext;
use PCIT\GPI\Webhooks\Handler\Handler;
use PCIT\GPI\Webhooks\Handler\Interfaces\PullRequestInterface;

abstract class PullRequestAbstract extends Handler implements PullRequestInterface
{
    public function pustomize(PullRequestContext $context, string $git_type): void
    {
        $context->git_type = $git_type;

        $this->callPustomize('PullRequest', $context);
    }
}

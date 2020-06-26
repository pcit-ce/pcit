<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\PullRequest;

use PCIT\GPI\Webhooks\Context\PullRequestContext;

interface HandlerInterface
{
    public function handle(PullRequestContext $context);
}

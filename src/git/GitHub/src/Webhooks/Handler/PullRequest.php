<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\PullRequestAbstract;

class PullRequest extends PullRequestAbstract
{
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\PullRequest::handle($webhooks_content);

        if (!\in_array($context->action, ['opened', 'synchronize'])) {
            return;
        }

        $this->handlePullRequest($context, 'github');
    }
}

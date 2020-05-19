<?php

declare(strict_types=1);

namespace PCIT\Gitee\Webhooks\Handler;

use PCIT\Gitee\Webhooks\Parser\PullRequest as PullRequestParser;
use PCIT\GPI\Webhooks\Handler\Abstracts\PullRequestAbstract;

class PullRequest extends PullRequestAbstract
{
    public $git_type = 'gitee';

    public function handle(string $webhooks_content): void
    {
        $context = PullRequestParser::handle($webhooks_content);
        $context->git_type = $this->git_type;

        $this->handlePullRequest($context, $this->git_type);
    }
}

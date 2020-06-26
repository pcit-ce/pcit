<?php

declare(strict_types=1);

namespace PCIT\Gitee\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Kernel as WebhooksHandlerKernel;

class Kernel extends WebhooksHandlerKernel
{
    public function note(string $webhooks_content, string $git_type): void
    {
        $this->issue_comment($webhooks_content, $git_type);
    }

    public function merge(string $webhooks_content, string $git_type): void
    {
        $this->pull_request($webhooks_content, $git_type);
    }

    public function issue(string $webhooks_content, string $git_type): void
    {
        $this->issues($webhooks_content, $git_type);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Gitee\Webhooks\Handler;

use PCIT\Gitee\Webhooks\Parser\Issues as IssuesParser;
use PCIT\GPI\Webhooks\Handler\Abstracts\IssuesAbstract;

class Issues extends IssuesAbstract
{
    public $git_type = 'gitee';

    public function handle(string $webhooks_content): void
    {
        $context = IssuesParser::handle($webhooks_content);

        $this->handleIssues($context, $this->git_type);
    }

    public function comment(string $webhooks_content): void
    {
        $context = IssuesParser::comment($webhooks_content);

        $this->handleComment($context, $this->git_type);
    }
}

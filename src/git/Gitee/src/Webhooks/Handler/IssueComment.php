<?php

declare(strict_types=1);

namespace PCIT\Gitee\Webhooks\Handler;

use PCIT\Gitee\Webhooks\Parser\Issues as IssuesParser;
use PCIT\GPI\Webhooks\Handler\Abstracts\IssueCommentAbstract;

class IssueComment extends IssueCommentAbstract
{
    public $git_type = 'gitee';

    public function handle(string $webhooks_content): void
    {
        $context = IssuesParser::comment($webhooks_content);

        $this->pustomize($context, $this->git_type);
    }
}

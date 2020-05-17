<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Issue;

use App\GetAccessToken;
use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\Pustomize\Interfaces\Issue\BasicInterface;

class Basic implements BasicInterface
{
    /** @var IssuesContext */
    public $context;

    /** @var \PCIT\PCIT */
    public $pcit;

    public function handle(IssuesContext $context): void
    {
        if ('opened' !== $context->action) {
            return;
        }

        $this->context = $context;

        $accessToken = GetAccessToken::getGitHubAppAccessToken($this->context->rid);

        /* @var \PCIT\PCIT $pcit */
        $this->pcit = app('pcit')->setAccessToken($accessToken);

        $body = <<<EOF
You can writing some word in a comment to trigger action:

* `/translate-title`
EOF;
        $this->createIssueComment($body);

        $this->translateTitle();
    }

    public function createIssueComment(string $body): void
    {
        $this->pcit->issue_comments->create(
            $this->context->repo_full_name,
            (int) $this->context->issue_number,
            $body
        );
    }

    public function translateTitle(): void
    {
        $this->pcit->issue->translateTitle(
            $this->context->repo_full_name,
            (int) $this->context->issue_number,
            (int) $this->context->rid,
            $this->context->title,
        );
    }
}

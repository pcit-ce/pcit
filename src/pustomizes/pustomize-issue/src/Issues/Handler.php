<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Issues;

use App\GetAccessToken;
use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\PCIT;
use PCIT\Pustomize\Interfaces\Issues\HandlerInterface;

class Handler implements HandlerInterface
{
    /** @var IssuesContext */
    public $context;

    /** @var \PCIT\PCIT */
    public $pcit;

    public function handle(IssuesContext $context): void
    {
        if (!\in_array($context->action, ['opened', 'open'])) {
            return;
        }

        $this->context = $context;

        $accessToken = GetAccessToken::byRepoFullName($context->repo_full_name, null, $context->git_type);

        // @var \PCIT\PCIT $pcit
        $this->pcit = new PCIT([], $context->git_type, $accessToken);

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
            $this->context->issue_number,
            $body
        );
    }

    public function translateTitle(): void
    {
        $this->pcit->issue->translateTitle(
            $this->context->repo_full_name,
            $this->context->issue_number,
            (int) $this->context->rid,
            $this->context->title,
        );
    }
}

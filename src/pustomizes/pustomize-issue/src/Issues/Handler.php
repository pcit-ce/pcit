<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Issues;

use App\GetAccessToken;
use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\Pustomize\Interfaces\Issues\HandlerInterface;
use PCIT\Subject;
use PCIT\UpdateUserInfo;

class Handler implements HandlerInterface
{
    /** @var IssuesContext */
    public $context;

    /** @var \PCIT\GPI\GPI */
    public $pcit;

    public function handle(IssuesContext $context): void
    {
        $git_type = $context->git_type;
        $installation_id = $context->installation->id;
        $action = $context->action;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $issue_number = $context->issue_number;
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type))
            ->handle();

        \Log::info('issue #'.$issue_number.' '.$action);

        if (!\in_array($context->action, ['opened', 'open'])) {
            return;
        }

        $this->context = $context;

        $accessToken = GetAccessToken::byRepoFullName($context->repo_full_name, null, $context->git_type);

        $this->pcit = \PCIT::git($git_type, $accessToken);

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

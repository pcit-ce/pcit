<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Context\IssuesContext;
use PCIT\GPI\Webhooks\Handler\Interfaces\IssuesInterface;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class IssuesAbstract implements IssuesInterface
{
    public function handleIssues(IssuesContext $context, string $git_type): void
    {
        $context->git_type = $git_type;
        $installation_id = $context->installation_id;
        $action = $context->action;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $issue_number = $context->issue_number;
        $account = $context->account;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type))
            ->handle();

        \Log::info('issue #'.$issue_number.' '.$action);

        // pustomize
        $this->triggerIssuesPustomize($context);
    }

    public function handleComment(IssueCommentContext $context, string $git_type): void
    {
        $context->git_type = $git_type;
        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $account = $context->account;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type))
            ->handle();

        // pustomize
        $this->triggerIssueCommentPustomize($context);
    }

    public function triggerIssuesPustomize(IssuesContext $context): void
    {
        $class = 'PCIT\\Pustomize\\Issue\\Basic';

        (new PustomizeHandler())->handle($class, $context);
    }

    public function triggerIssueCommentPustomize(IssueCommentContext $context): void
    {
        $class = 'PCIT\\Pustomize\\Issue\\Comment';

        (new PustomizeHandler())->handle($class, $context);
    }
}

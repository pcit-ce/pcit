<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

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
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type))
            ->handle();

        \Log::info('issue #'.$issue_number.' '.$action);

        // pustomize
        (new PustomizeHandler())->handle('Issues', $context);
    }
}

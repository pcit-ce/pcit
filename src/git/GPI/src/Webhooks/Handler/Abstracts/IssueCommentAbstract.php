<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use PCIT\GPI\Webhooks\Context\IssueCommentContext;
use PCIT\GPI\Webhooks\Handler\Interfaces\IssueCommentInterface;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class IssueCommentAbstract implements IssueCommentInterface
{
    public function handleIssueComment(IssueCommentContext $context, string $git_type): void
    {
        $context->git_type = $git_type;
        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type))
            ->handle();

        // pustomize
        (new PustomizeHandler())->handle('IssueComment', $context);
    }
}

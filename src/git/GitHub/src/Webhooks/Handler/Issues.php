<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\IssuesAbstract;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;

class Issues extends IssuesAbstract
{
    /**
     *  "assigned", "unassigned",
     *  "labeled", "unlabeled",
     *  "opened", "closed" or "reopened"
     *  "edited"
     *  "milestoned", "demilestoned".
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $issuesContext = \PCIT\GitHub\Webhooks\Parser\Issues::handle($webhooks_content);

        $installation_id = $issuesContext->installation_id;
        $action = $issuesContext->action;
        $rid = $issuesContext->rid;
        $repo_full_name = $issuesContext->repo_full_name;
        $issue_number = $issuesContext->issue_number;
        $account = $issuesContext->account;
        $default_branch = $issuesContext->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch))
            ->handle();

        \Log::info('issue #'.$issue_number.' '.$action);

        // pustomize
        $this->triggerIssuesPustomize($issuesContext);
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @throws \Exception
     */
    public function comment(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Issues::comment($webhooks_content);

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $account = $context->account;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch))
            ->handle();

        // pustomize
        $this->triggerIssueCommentPustomize($context);
    }
}

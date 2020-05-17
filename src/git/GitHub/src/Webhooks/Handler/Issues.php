<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Issues\IssuesAbstract;

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

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
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
        $issueCommentContext = \PCIT\GitHub\Webhooks\Parser\Issues::comment($webhooks_content);

        $installation_id = $issueCommentContext->installation_id;
        $rid = $issueCommentContext->rid;
        $repo_full_name = $issueCommentContext->repo_full_name;
        $account = $issueCommentContext->account;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        // pustomize
        $this->triggerIssueCommentPustomize($issueCommentContext);
    }
}

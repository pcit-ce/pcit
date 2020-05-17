<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Build;
use PCIT\GPI\Webhooks\Handler\PullRequestAbstract;

class PullRequest extends PullRequestAbstract
{
    /**
     * Action.
     *
     * "assigned", "unassigned", "review_requested", "review_request_removed",
     * "labeled", "unlabeled", "opened", "synchronize", "edited", "closed", or "reopened"
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $pullRequestContext = \PCIT\GitHub\Webhooks\Parser\PullRequest::handle($webhooks_content);

        $installation_id = $pullRequestContext->installation_id;
        $action = $pullRequestContext->action;
        $rid = $pullRequestContext->rid;
        $repo_full_name = $pullRequestContext->repo_full_name;
        $commit_id = $pullRequestContext->commit_id;
        $event_time = $pullRequestContext->event_time;
        $commit_message = $pullRequestContext->commit_message;
        $committer_username = $pullRequestContext->committer_username;
        $committer_uid = $pullRequestContext->committer_uid;
        $pull_request_number = $pullRequestContext->pull_request_number;
        $branch = $pullRequestContext->branch;
        $internal = $pullRequestContext->internal;
        $pull_request_source = $pullRequestContext->pull_request_source;
        $account = $pullRequestContext->account;

        $subject = new Subject();

        $subject->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name));

        $config_array = $subject->register(new GetConfig($rid, $commit_id))->handle()->config_array;

        $config = json_encode($config_array);

        $last_insert_id = Build::insertPullRequest(
            $event_time,
            $action,
            $commit_id,
            $commit_message,
            (int) $committer_uid,
            $committer_username,
            $pull_request_number,
            $branch,
            $rid,
            $config,
            $internal,
            $pull_request_source
        );

        $subject->register(new Skip($commit_message, (int) $last_insert_id, $branch, $config))
            ->handle();

        \Storage::put('github/events/'.$last_insert_id.'.json', $webhooks_content);

        // pustomize
        $this->triggerPullRequestPustomize($pullRequestContext);
    }
}

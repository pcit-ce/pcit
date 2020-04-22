<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Build;
use PCIT\GitHub\Webhooks\Parser\PullRequestContext;
use PCIT\GitHub\Webhooks\PustomizeHandler;

class PullRequest
{
    /**
     * Action.
     *
     * "assigned", "unassigned", "review_requested", "review_request_removed",
     * "labeled", "unlabeled", "opened", "synchronize", "edited", "closed", or "reopened"
     *
     * @throws \Exception
     */
    public static function handle(string $webhooks_content): void
    {
        $pull_request_parser_metadata = \PCIT\GitHub\Webhooks\Parser\PullRequest::handle($webhooks_content);

        [
            'installation_id' => $installation_id,
            'action' => $action,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'commit_id' => $commit_id,
            'event_time' => $event_time,
            'commit_message' => $commit_message,
            'committer_username' => $committer_username,
            'committer_uid' => $committer_uid,
            'pull_request_number' => $pull_request_number,
            'branch' => $branch,
            'internal' => $internal,
            'pull_request_source' => $pull_request_source,
            'account' => $account,
        ] = $pull_request_parser_metadata;

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

        $class = 'PCIT\\Pustomize\\PullRequest\\Basic';

        $context = new PullRequestContext($pull_request_parser_metadata, $webhooks_content);

        (new PustomizeHandler())->handle($class, $context);
    }
}

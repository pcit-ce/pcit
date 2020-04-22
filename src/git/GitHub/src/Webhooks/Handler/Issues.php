<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GitHub\Webhooks\Parser\IssueCommentContext;
use PCIT\GitHub\Webhooks\Parser\IssuesContext;
use PCIT\GitHub\Webhooks\PustomizeHandler;

class Issues
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
    public static function handle(string $webhooks_content): void
    {
        $issue_parser_metadata = \PCIT\GitHub\Webhooks\Parser\Issues::handle($webhooks_content);

        [
            'installation_id' => $installation_id,
            'action' => $action,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'issue_id' => $issue_id,
            'issue_number' => $issue_number,
            'title' => $title,
            'body' => $body,
            'sender_username' => $sender_username,
            'sender_uid' => $sender_uid,
            'sender_pic' => $sender_pic,
            'state' => $state,
            'locked' => $locked,
            'created_at' => $created_at,
            'closed_at' => $closed_at,
            'updated_at' => $updated_at,
            'assignees' => $assignees,
            'labels' => $labels,
            'account' => $account,
        ] = $issue_parser_metadata;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        \Log::info('issue #'.$issue_number.' '.$action);

        // pustomize
        $class = 'PCIT\\Pustomize\\Issue\\Basic';

        $context = new IssuesContext($issue_parser_metadata, $webhooks_content);

        (new PustomizeHandler())->handle($class, $context);
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @throws \Exception
     */
    public static function comment(string $webhooks_content): void
    {
        $issue_comment_parser_metadata = \PCIT\GitHub\Webhooks\Parser\Issues::comment($webhooks_content);
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'action' => $action,
            'issue_id' => $issue_id,
            'comment_id' => $comment_id,
            'issue_number' => $issue_number,
            'updated_at' => $updated_at,
            'sender_uid' => $sender_uid,
            'body' => $body,
            'created_at' => $created_at,
            'account' => $account,
        ] = $issue_comment_parser_metadata;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        // pustomize
        $class = 'PCIT\\Pustomize\\Issue\\Comment';

        $context = new IssueCommentContext($issue_comment_parser_metadata, $webhooks_content);

        (new PustomizeHandler())->handle($class, $context);
    }
}

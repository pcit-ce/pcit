<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\GetAccessToken;
use App\Issue;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\Log;

class Issues
{
    private static $cache_key_github_issue = 'github_issue';

    /**
     *  "assigned", "unassigned",
     *  "labeled",  "unlabeled",
     *  "opened",   "edited", "closed" or "reopened"
     *  "milestoned", "demilestoned".
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
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
        ] = \KhsCI\Support\Webhooks\GitHub\Issues::handle($json_content);

        $assignees && Issue::updateAssignees($assignees, 'github', $issue_id);

        $labels && Issue::updateLabels($labels, 'github', $issue_id);

        if (\in_array($action, ['opened', 'edited', 'closed' or 'reopened'], true)) {
            Issue::insert(
                'github', $rid, $issue_id, $issue_number, $action, $title, $body,
                $sender_username, $sender_uid, $sender_pic,
                $state, (int) $locked,
                $created_at, $closed_at, $updated_at
            );
        }

        if ('opened' !== $action) {
            return;
        }

        self::createComment($rid, $repo_full_name, $issue_number, $body);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        Log::debug(__FILE__, __LINE__, $issue_number.' opened', [], Log::INFO);

        return;
    }

    /**
     * "created", "edited", or "deleted".
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function comment($json_content): void
    {
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
        ] = \KhsCI\Support\Webhooks\GitHub\Issues::comment($json_content);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        if ('edited' === $action) {
            Log::debug(__FILE__, __LINE__, 'Edit Issue Comment SKIP', [], Log::INFO);

            return;
        }

        if ('deleted' === $action) {
            $output = Issue::comment_deleted($issue_id, $comment_id, $updated_at);

            if (1 === $output) {
                $debug_info = 'Delete Issue Comment SUCCESS';

                Log::debug(__FILE__, __LINE__, $debug_info, [], Log::INFO);
            }

            return;
        }

        $last_insert_id = Issue::insertComment($rid, $issue_id, $comment_id, $issue_number, $body, $sender_uid, $created_at);

        Cache::store()->lPush(self::$cache_key_github_issue, $last_insert_id);

        self::createComment($rid, $repo_full_name, $issue_number, $body);

        Log::debug(__FILE__, __LINE__, 'Create AI Bot Issue Comment', [], Log::INFO);
    }

    /**
     * @param $rid
     * @param $repo_full_name
     * @param $issue_number
     * @param $body
     *
     * @throws \Exception
     */
    private static function createComment($rid, $repo_full_name, $issue_number, $body): void
    {
        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        (new KhsCI(['github_access_token' => $access_token]))
            ->issue_comments->create($repo_full_name, $issue_number, $body);
    }
}

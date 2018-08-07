<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\GetAccessToken;
use App\Issue;
use App\Repo;
use App\User;
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
            'username' => $username,
            'rid' => $rid,
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
            'assigness' => $assignees,
            'labels' => $labels
        ] = \KhsCI\Support\Webhooks\GitHub\Issues::handle($json_content);

        if ($assignees) {
            foreach ($assignees as $k) {
                Issue::updateAssignees($k, 'github', $issue_id);
            }
        }

        if ($labels) {
            foreach ($labels as $k) {
                Issue::updateLabels($k, 'github', $issue_id);
            }
        }

        if (in_array($action, ['opened', 'edited', 'closed' or 'reopened'])) {
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

        $repo_full_name = Repo::getRepoFullName('github', $rid);

        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $khsci = new KhsCI(['github_access_token' => $access_token], 'github');

        $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

        Repo::updateGitHubInstallationIdByRid('github', (int) $rid, $repo_full_name, (int) $installation_id);
        User::updateInstallationId('github', (int) $installation_id, $username);

        Log::debug(__FILE__, __LINE__, $issue_number.' opened', [], Log::INFO);

        return;
    }

    /**
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function comment($json_content): void
    {
        [
            'installation' => $installation_id,
            'username' => $username,
            'rid' => $rid,
            'action' => $action,
            'issue_id' => $issue_id,
            'comment_id' => $comment_id,
            'issue_number' => $issue_number,
            'updated_at' => $updated_at,
            'sender_uid' => $sender_uid,
            'body' => $body,
            'created_at' => $created_at
        ] = \KhsCI\Support\Webhooks\GitHub\Issues::comment($json_content);

        $repo_full_name = Repo::getRepoFullName('github', $rid);
        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $khsci = new KhsCI(['github_access_token' => $access_token]);

        if ('edited' === $action) {
            Log::debug(__FILE__, __LINE__, 'Edit Issue Comment SKIP', [], Log::INFO);

            return;
        }

        if ('deleted' === $action) {
            $output = Issue::comment_deleted('github', $issue_id, $comment_id, $updated_at);

            if (1 === $output) {
                $debug_info = 'Delete Issue Comment SUCCESS';

                Log::debug(__FILE__, __LINE__, $debug_info, [], Log::INFO);
            }

            return;
        }

        $last_insert_id = Issue::insertComment(
            'github', $rid, $issue_id, $comment_id, $issue_number, $body,
            $sender_uid, $created_at
        );

        Cache::connect()->lPush(self::$cache_key_github_issue, $last_insert_id);

        $khsci->issue_comments->create($repo_full_name, $issue_number, $body);

        $debug_info = 'Create Bot Issue Comment By Issue Comment ADD';

        Log::debug(__FILE__, __LINE__, $debug_info, [], Log::INFO);

        Repo::updateGitHubInstallationIdByRid('github', (int) $rid, $repo_full_name, (int) $installation_id);
        User::updateInstallationId('github', (int) $installation_id, $username);
    }
}

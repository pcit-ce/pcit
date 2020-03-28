<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\GetAccessToken;
use PCIT\PCIT;

class Issues
{
    /**
     *  "assigned", "unassigned",
     *  "labeled", "unlabeled",
     *  "opened", "closed" or "reopened"
     *  "edited"
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
        ] = \PCIT\GitHub\Webhooks\Parser\Issues::handle($json_content);

        if ('opened' !== $action) {
            return;
        }

        self::translateTitle($repo_full_name, (int) $issue_number, (int) $rid, $title);

        // self::createComment($rid, $repo_full_name, $issue_number, $body);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        \Log::info($issue_number.' opened', []);

        return;
    }

    /**
     * 检查标题是否为中文.
     *
     * 若为中文则翻译为英文
     *
     * @param $title
     * @param $rid
     * @param $repo_full_name
     * @param $issue_number
     *
     * @throws \Exception
     */
    public static function translateTitle(string $repo_full_name,
                                          int $issue_number,
                                          ?int $rid,
                                          ?string $title): void
    {
        $access_token = GetAccessToken::getGitHubAppAccessToken($rid ?: null, $repo_full_name);

        $app = new PCIT(['github_access_token' => $access_token]);

        if (!$title) {
            // get issue title
            $result = $app->issue->getSingle($repo_full_name, $issue_number);

            $title = json_decode($result)->title;
        }

        try {
            $result = $app->tencent_ai->translate->detect($title);

            $lang = $result['data']['lang'] ?? 'en';

            if ('zh' === $lang) {
                $result = $app->tencent_ai->translate->aILabText($title, 1);

                $title = $result['data']['trans_text'] ?? null;
            }
        } catch (\Throwable $e) {
            \Log::info($e->__toString());

            return;
        }

        if ('zh' !== $lang or null === $title) {
            return;
        }

        $app->issue->edit($repo_full_name, $issue_number, $title);
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
        ] = \PCIT\GitHub\Webhooks\Parser\Issues::comment($json_content);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        if ('edited' === $action) {
            \Log::info('Edit Issue Comment SKIP', []);

            return;
        }

        if ('deleted' === $action) {
            return;
        }

        // self::createComment($rid, $repo_full_name, $issue_number, $body);

        // \Log::info('Create AI Bot Issue Comment', []);
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

        (new PCIT(['github_access_token' => $access_token]))
            ->issue_comments->create($repo_full_name, $issue_number, $body);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks;

use App\Build;
use App\Console\Webhooks\GetConfig;
use App\Console\Webhooks\Skip;

class Push
{
    /**
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        $result = PCIT\GitHub\WebhooksParse\Push::handle($json_content);

        $tag = $result['tag'] ?? null;

        if ($tag) {
            self::tag($result);

            return;
        }

        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'commit_id' => $commit_id,
            'commit_message' => $commit_message,
            'committer' => $committer,
            'author' => $author,
            'compare' => $compare,
            'event_time' => $event_time,
            'account' => $account,
            'sender' => $sender
        ] = $result;

        // user table not include user info
        $subject = new Subject();

        $subject->register(
            new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $sender));

        $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;

        $config = json_encode($config_array);

        $last_insert_id = Build::insert('push', $branch, $compare, $commit_id,
            $commit_message, $committer->name, $committer->email, $committer->username,
            $author->name, $author->email, $author->username,
            $rid, $event_time, $config);

        $subject->register(new Skip($commit_message, (int) $last_insert_id, $branch, $config))
            ->handle();
    }

    /**
     * @param array $content
     *
     * @throws \Exception
     */
    public static function tag(array $content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'tag' => $tag,
            'commit_id' => $commit_id,
            'commit_message' => $commit_message,
            'committer' => $committer,
            'author' => $author,
            'event_time' => $event_time,
            'account' => $account,
            'sender' => $sender
        ] = $content;

        $subject = new Subject();

        $subject->register(
            new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $sender));

        $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;

        $config = json_encode($config_array);

        $last_insert_id = Build::insertTag(
            $branch, $tag, $commit_id, $commit_message,
            $committer->name, $committer->email, $committer->username,
            $author->name, $author->email, $author->username,
            $rid, $event_time, $config
        );

        Build::updateBuildStatus((int) $last_insert_id, 'pending');
    }
}

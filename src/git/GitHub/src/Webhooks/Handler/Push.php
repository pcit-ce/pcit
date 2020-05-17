<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Build;
use PCIT\GPI\Webhooks\Handler\PushAbstract;

class Push extends PushAbstract
{
    /**
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Push::handle($webhooks_content);

        $tag = $context->tag ?? null;

        if ($tag) {
            $this->tag($context);

            return;
        }

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $branch = $context->branch;
        $commit_id = $context->commit_id;
        $commit_message = $context->commit_message;
        $committer = $context->committer;
        $author = $context->author;
        $compare = $context->compare;
        $event_time = $context->event_time;
        $account = $context->account;
        $sender = $context->sender;
        $private = $context->private;

        // user table not include user info
        $subject = new Subject();

        $subject->register(
            new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $sender));

        $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;

        $config = json_encode($config_array);

        $last_insert_id = Build::insert('push', $branch, $compare, $commit_id,
            $commit_message, $committer->name, $committer->email, $committer->username,
            $author->name, $author->email, $author->username,
            $rid, $event_time, $config, $private);

        $subject->register(new Skip($commit_message, (int) $last_insert_id, $branch, $config))
            ->handle();

        \Storage::put('github/events/'.$last_insert_id.'.json', $webhooks_content);
    }

    /**
     * @throws \Exception
     */
    public function tag($context): void
    {
        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $branch = $context->branch;
        $tag = $context->tag;
        $commit_id = $context->commit_id;
        $commit_message = $context->commit_message;
        $committer = $context->committer;
        $author = $context->author;
        $event_time = $context->event_time;
        $account = $context->account;
        $sender = $context->sender;

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

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use App\Build;
use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Handler\GetConfig;
use PCIT\GPI\Webhooks\Handler\Interfaces\PushInterface;
use PCIT\GPI\Webhooks\Handler\Skip;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;

abstract class PushAbstract implements PushInterface
{
    public function handle_push(Context $context, string $git_type = 'github'): void
    {
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

        $config_array = $subject->register(new GetConfig((int) $rid, $commit_id, $git_type))->handle()->config_array;

        $config = json_encode($config_array);

        $last_insert_id = Build::insert('push', $branch, $compare, $commit_id,
            $commit_message, $committer->name, $committer->email, $committer->username,
            $author->name, $author->email, $author->username,
            $rid, $event_time, $config, $private, $git_type);

        $subject->register(new Skip($commit_message, (int) $last_insert_id, $branch, $config))
            ->handle();

        \Storage::put($git_type.'/events/'.$last_insert_id.'.json', $context->raw);
    }
}

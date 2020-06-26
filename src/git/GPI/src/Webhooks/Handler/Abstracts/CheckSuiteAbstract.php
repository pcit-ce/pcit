<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use App\Build;
use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\TagContext;
use PCIT\GPI\Webhooks\Handler\GetConfig;
use PCIT\GPI\Webhooks\Handler\Skip;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;

abstract class CheckSuiteAbstract
{
    public function handlePush(Context $context, string $git_type): void
    {
        $context->git_type = $git_type;
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
        $owner = $context->owner;
        $sender = $context->sender;
        $private = $context->private;
        $default_branch = $context->repository->default_branch;

        // user table not include user info
        $subject = new Subject();

        $subject->register(
            new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, $sender, $git_type));

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

    public function handleTag(TagContext $context, string $git_type): void
    {
        $context->git_type = $git_type;
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
        $owner = $context->owner;
        $sender = $context->sender;
        $private = $context->private;
        $default_branch = $context->repository->default_branch;

        $subject = new Subject();

        $subject->register(
            new UpdateUserInfo($owner, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, $sender, $git_type));

        $config_array = $subject->register(new GetConfig((int) $rid, $commit_id, $git_type))->handle()->config_array;

        $config = json_encode($config_array);

        $last_insert_id = Build::insertTag(
            $branch, $tag, $commit_id, $commit_message,
            $committer->name, $committer->email, $committer->username,
            $author->name, $author->email, $author->username,
            $rid, $event_time, $config, $private, $git_type
        );

        Build::updateBuildStatus((int) $last_insert_id, 'pending');

        \Storage::put($git_type.'/events/'.$last_insert_id.'.json', $context->raw);
    }
}

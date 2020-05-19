<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler\Abstracts;

use App\Build;
use PCIT\GPI\Webhooks\Context\PullRequestContext;
use PCIT\GPI\Webhooks\Handler\GetConfig;
use PCIT\GPI\Webhooks\Handler\Interfaces\PullRequestInterface;
use PCIT\GPI\Webhooks\Handler\Skip;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;
use PCIT\GPI\Webhooks\PustomizeHandler;

abstract class PullRequestAbstract implements PullRequestInterface
{
    public function handlePullRequest(PullRequestContext $context, string $git_type): void
    {
        $context->git_type = $git_type;
        $installation_id = $context->installation_id;
        $action = $context->action;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $commit_id = $context->commit_id;
        $event_time = $context->event_time;
        $commit_message = $context->commit_message;
        $committer_username = $context->committer_username;
        $committer_uid = $context->committer_uid;
        $pull_request_number = $context->pull_request_number;
        $branch = $context->branch;
        $internal = $context->internal;
        $pull_request_source = $context->pull_request_source;
        $account = $context->account;
        $default_branch = $context->repository->default_branch;
        $private = $context->private;

        $subject = new Subject();

        $subject->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch, null, $git_type));

        $config_array = $subject->register(new GetConfig($rid, $commit_id, $git_type))->handle()->config_array;

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
            $pull_request_source,
            $private,
            $git_type
        );

        $subject->register(new Skip($commit_message, (int) $last_insert_id, $branch, $config))
            ->handle();

        \Storage::put($git_type.'/events/'.$last_insert_id.'.json', $context->raw);

        // pustomize
        $this->triggerPullRequestPustomize($context);
    }

    public function triggerPullRequestPustomize(PullRequestContext $context): void
    {
        $class = 'PCIT\\Pustomize\\PullRequest\\Basic';

        (new PustomizeHandler())->handle($class, $context);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Pustomize\CheckSuite;

use App\Build;
use PCIT\GetConfig;
use PCIT\GPI\Webhooks\Context\CheckSuiteContext;
use PCIT\Skip;
use PCIT\Subject;
use PCIT\UpdateUserInfo;
use Symfony\Component\Yaml\Exception\ParseException;

class Handler
{
    /** @var CheckSuiteContext */
    private $context;

    public function handle(CheckSuiteContext $context): void
    {
        $this->context = $context;

        $git_type = $context->git_type;

        if (!\in_array($context->action, ['requested'])) {
            return;
        }

        if ('requested' === $context->action) {
            if ($context->check_suite->pull_requests) {
                $this->handlePullRequest($context);

                return;
            }
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
        $owner = $context->owner;
        $sender = $context->sender;
        $private = $context->private;
        $default_branch = $context->repository->default_branch;

        // user table not include user info
        $subject = new Subject();

        $subject->register(
            new UpdateUserInfo(
                $owner,
                (int) $installation_id,
                (int) $rid,
                $repo_full_name,
                $default_branch,
                $sender,
                $git_type
            )
        );

        try {
            $config_array = $subject->register(
                new GetConfig((int) $rid, $commit_id, $git_type)
            )->handle()->config_array;

            $config = json_encode($config_array);
        } catch (ParseException $e) {
            $config = $e->getMessage();
            $build_status = 'misconfigured';
        }

        $last_insert_id = Build::insert(
            'push',
            $branch,
            $compare,
            $commit_id,
            $commit_message,
            $committer->name,
            $committer->email,
            $committer->username,
            $author->name,
            $author->email,
            $author->username,
            $rid,
            $event_time,
            $config,
            $private,
            $git_type
        );

        $subject->register(
            new Skip($commit_message, (int) $last_insert_id, $branch, $config)
        )->handle();

        if ($build_status ?? false) {
            Build::updateBuildStatus($last_insert_id, $build_status);

            return;
        }

        \Storage::put(
            'pcit/events/'.$git_type.'/'.$last_insert_id.'/event.json',
            $context->raw
        );
    }

    public function handlePullRequest(CheckSuiteContext $context): void
    {
        $git_type = $context->git_type;
        $installation_id = $context->installation_id;
        $action = $context->action;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $commit_id = $context->commit_id;
        $event_time = $context->event_time;
        $commit_message = $context->commit_message;
        $committer_username = $context->committer->name;
        $committer_uid = $context->sender->uid;
        $pull_request = $context->check_suite->pull_requests[0];
        $pull_request_number = $pull_request->number;
        $branch = $pull_request->base->ref;
        $internal = $pull_request->base->repo->id === $pull_request->head->repo->id;
        $source_repo_api_url_array = explode('/', $pull_request->head->repo->url);
        $pull_request_source = $source_repo_api_url_array[4].'/'.$source_repo_api_url_array[5];
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;
        $private = $context->private;

        $subject = new Subject();

        $subject->register(new UpdateUserInfo(
            $owner,
            (int) $installation_id,
            (int) $rid,
            $repo_full_name,
            $default_branch,
            null,
            $git_type
        ));

        try {
            $config_array = $subject->register(
                new GetConfig($rid, $commit_id, $git_type)
            )->handle()->config_array;

            $config = json_encode($config_array);
        } catch (ParseException $e) {
            $config = $e->getMessage();
            $build_status = 'misconfigured';
        }

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
        if ($build_status ?? false) {
            Build::updateBuildStatus($last_insert_id, $build_status);

            return;
        }
        $subject->register(
            new Skip($commit_message, (int) $last_insert_id, $branch, $config)
        )
            ->handle();

        \Storage::put('pcit/events/'.$git_type.'/'.$last_insert_id.'/event.json', $context->raw);
    }
}

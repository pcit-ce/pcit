<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\CheckRunContext;
use PCIT\GPI\Webhooks\Context\CheckSuiteContext;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Author;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Committer;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

class Check
{
    /**
     * @throws \Exception
     */
    public static function suite(string $webhooks_content): CheckSuiteContext
    {
        $obj = json_decode($webhooks_content);

        $installation_id = $obj->installation->id ?? null;

        $repository = $obj->repository;
        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'Check Suite', 'action' => $action]);

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $check_suite = $obj->check_suite;
        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;
        $commit_id = $check_suite->head_sha;

        $org = ($obj->organization ?? false) ? true : false;

        $context = new CheckSuiteContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $rid;
        $context->repo_full_name = $repo_full_name;
        $context->action = $action;
        $context->branch = $branch;
        $context->commit_id = $commit_id;
        $context->commit_message = $check_suite->head_commit->message;
        $context->check_suite_id = $check_suite_id;
        $context->account = new Account($repository_owner, $org);
        $context->ref = 'refs/heads/'.$branch;
        $context->base_ref = '';
        $context->forced = false;
        $context->before = $check_suite->before;
        $context->after = $check_suite->after;
        $context->compare = 'https://github.com/'.$repo_full_name.'/compare/'.$context->before.'...'.$context->after;
        $context->head_commit = $check_suite->head_commit;
        $context->event_time = Date::parse($check_suite->head_commit->timestamp);
        $context->author = new Author($check_suite->head_commit->author);
        $context->committer = new Committer($check_suite->head_commit->committer);
        $context->sender = new Sender($obj->sender);
        $context->private = $repository->private;

        return $context;
    }

    /**
     * @throws \Exception
     */
    public static function run(string $webhooks_content): CheckRunContext
    {
        $obj = json_decode($webhooks_content);

        $action = $obj->action;

        \Log::info('Receive event', ['type' => 'Check Run', 'action' => $action]);

        $installation_id = $obj->installation->id ?? null;
        $repository = $obj->repository;
        $rid = $repository->id;
        $repo_full_name = $repository->full_name;

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $check_run = $obj->check_run;

        $check_run_id = $check_run->id;
        $commit_id = $check_run->head_sha;
        $external_id = $check_run->external_id;
        $check_suite = $check_run->check_suite;

        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;

        $org = ($obj->organization ?? false) ? true : false;

        $account = new Account($repository_owner, $org);

        $context = new CheckRunContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $rid;
        $context->repo_full_name = $repo_full_name;
        $context->action = $action;
        $context->branch = $branch;
        $context->commit_id = $commit_id;
        $context->check_suite_id = $check_suite_id;
        $context->check_run_id = $check_run_id;
        $context->external_id = $external_id;
        $context->account = $account;

        return $context;
    }
}

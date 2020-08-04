<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\CheckSuiteContext;
use PCIT\GPI\Webhooks\Context\Components\HeadCommit;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Author;
use PCIT\GPI\Webhooks\Context\Components\User\Committer;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;

class CheckSuite
{
    /**
     * @throws \Exception
     */
    public static function handle(string $webhooks_content): CheckSuiteContext
    {
        $obj = json_decode($webhooks_content);

        $installation_id = $obj->installation->id ?? null;

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        \Log::info('Receive event', ['type' => 'Check Suite', 'action' => $obj->action]);

        $check_suite = $obj->check_suite;
        $head_commit = new HeadCommit($check_suite->head_commit);
        $head_commit->timestamp_int = Date::parse($head_commit->timestamp);
        $head_commit->author = new Author($head_commit->author);
        $head_commit->committer = new Committer($head_commit->committer);
        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;
        $commit_id = $check_suite->head_sha;

        $context = new CheckSuiteContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;
        $context->action = $obj->action;
        $context->branch = $branch;
        $context->commit_id = $commit_id;
        $context->commit_message = $head_commit->message;
        $context->check_suite_id = $check_suite_id;
        $context->owner = $repository->owner;
        $context->ref = 'refs/heads/'.$branch;
        $context->base_ref = '';
        $context->forced = false;
        $context->before = $check_suite->before;
        $context->after = $check_suite->after;
        $context->compare = 'https://github.com/'.$context->full_name.'/compare/'.$context->before.'...'.$context->after;
        $context->head_commit = $head_commit;
        $context->event_time = $head_commit->timestamp_int;
        $context->author = $head_commit->author;
        $context->committer = $head_commit->committer;
        $context->sender = new Sender($obj->sender);
        $context->private = $repository->private;

        return $context;
    }
}

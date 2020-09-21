<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\CheckSuiteContext;

class CheckSuite
{
    /**
     * @throws \Exception
     */
    public static function handle(string $webhooks_content): CheckSuiteContext
    {
        $context = new CheckSuiteContext([], $webhooks_content);

        // \Log::info('Receive event', ['type' => 'Check Suite', 'action' => $context->action]);

        $check_suite = $context->check_suite;
        $head_commit = $check_suite->head_commit;
        $repository = $context->repository;

        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;
        $context->branch = $check_suite->head_branch;
        $context->commit_id = $check_suite->head_sha;
        $context->commit_message = $head_commit->message;
        $context->check_suite_id = $check_suite->id;
        $context->owner = $repository->owner;
        $context->ref = 'refs/heads/'.$context->branch;
        $context->base_ref = '';
        $context->forced = false;
        $context->before = $check_suite->before;
        $context->after = $check_suite->after;
        $context->compare = 'https://github.com/'.$repository->full_name.'/compare/'.$context->before.'...'.$context->after;
        $context->head_commit = $head_commit;
        $context->event_time = $head_commit->timestamp_int;
        $context->author = $head_commit->author;
        $context->committer = $head_commit->committer;
        $context->private = $repository->private;

        return $context;
    }
}

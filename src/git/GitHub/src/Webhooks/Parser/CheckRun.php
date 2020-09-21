<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\CheckRunContext;

class CheckRun
{
    /**
     * @throws \Exception
     */
    public static function handle(string $webhooks_content): CheckRunContext
    {
        $context = new CheckRunContext([], $webhooks_content);

        // \Log::info('Receive event', ['type' => 'Check Run', 'action' => $context->action]);

        $check_run = $context->check_run;
        $check_suite = $check_run->check_suite;
        $repository = $context->repository;

        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;
        $context->branch = $check_suite->head_branch;
        $context->commit_id = $check_run->head_sha;
        $context->check_suite_id = $check_suite->id;
        $context->check_run_id = $check_run->id;
        $context->external_id = $check_run->external_id;
        $context->owner = $repository->owner;

        return $context;
    }
}

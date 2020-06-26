<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GPI\Webhooks\Context\CheckRunContext;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Owner;

class CheckRun
{
    /**
     * @throws \Exception
     */
    public static function handle(string $webhooks_content): CheckRunContext
    {
        $obj = json_decode($webhooks_content);

        \Log::info('Receive event', ['type' => 'Check Run', 'action' => $obj->action]);

        $installation_id = $obj->installation->id ?? null;

        $repository = new Repository($obj->repository);
        $org = ($obj->organization ?? false) ? true : false;
        $repository->owner = new Owner($repository->owner, $org);

        $check_run = $obj->check_run;

        $check_run_id = $check_run->id;
        $commit_id = $check_run->head_sha;
        $external_id = $check_run->external_id;
        $check_suite = $check_run->check_suite;

        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;

        $context = new CheckRunContext([], $webhooks_content);

        $context->installation_id = $installation_id;
        $context->rid = $repository->id;
        $context->repo_full_name = $repository->full_name;
        $context->action = $obj->action;
        $context->branch = $branch;
        $context->commit_id = $commit_id;
        $context->check_suite_id = $check_suite_id;
        $context->check_run_id = $check_run_id;
        $context->external_id = $external_id;
        $context->owner = $repository->owner;

        return $context;
    }
}

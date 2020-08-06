<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Events\GetBuild;
use App\Job;
use PCIT\GPI\Webhooks\Handler\Abstracts\CheckRunAbstract;
use PCIT\Runner\Client as JobGenerator;

class CheckRun extends CheckRunAbstract
{
    public $git_type = 'github';

    /**
     * created updated rerequested requested_action.
     *
     * rerequested 用户点击了重新运行(Re-run)按钮
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\CheckRun::handle($webhooks_content);

        if (!\in_array($context->action, ['rerequested'], true)) {
            return;
        }

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $action = $context->action;
        $commit_id = $context->commit_id;
        $external_id = $context->external_id;
        $check_suite_id = $context->check_suite_id;
        $check_run_id = $context->check_run_id;
        $branch = $context->branch;
        $owner = $context->owner;
        $default_branch = $context->repository->default_branch;

        // 用户点击了某一 run 的 Re-run
        if ('rerequested' === $action) {
            $build_id = Job::getBuildKeyId((int) $external_id);

            (new JobGenerator())->handle((new GetBuild())->handle($build_id), (int) $external_id);

            return;
        }

        // 用户点击了按钮，CI 推送修复补丁
        // 'requested_action' === $action &&
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Events\Build;
use App\Job;
use PCIT\GPI\Webhooks\Handler\Abstracts\CheckAbstract;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;
use PCIT\Runner\Client as JobGenerator;

class Check extends CheckAbstract
{
    /**
     * completed.
     *
     * requested: when new code is pushed to the app's repository
     *
     * rerequested: re-run the entire check suite
     *
     * @throws \Exception
     */
    public function suite(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Check::suite($webhooks_content);

        if (!\in_array($context->action, ['requested'])) {
            return;
        }

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $branch = $context->branch;
        $commit_id = $context->commit_id;
        $action = $context->action;
        $account = $context->account;
        $check_suite_id = $context->check_suite_id;
        $default_branch = $context->repository->default_branch;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name, $default_branch))
            ->handle();

        if ('requested' === $action) {
            if ($context->check_suite->pull_requests) {
            } else {
                $this->handlePush($context, 'github');
            }

            return;
        }

        // 'rerequested' === $action && Build::updateBuildStatusByCommitId('pending', (int) $rid, $branch, $commit_id);
    }

    /**
     * created updated rerequested requested_action.
     *
     * rerequested 用户点击了重新运行(Re-run)按钮
     *
     * @throws \Exception
     */
    public function run(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Check::run($webhooks_content);

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
        $account = $context->account;
        $default_branch = $context->repository->default_branch;

        // 用户点击了某一 run 的 Re-run
        if ('rerequested' === $action) {
            $build_id = Job::getBuildKeyId((int) $external_id);

            (new JobGenerator())->handle((new Build())->handle($build_id), (int) $external_id);

            return;
        }

        // 用户点击了按钮，CI 推送修复补丁
        // 'requested_action' === $action &&
    }
}

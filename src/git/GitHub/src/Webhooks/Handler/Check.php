<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Build;
use App\Job;
use App\Notifications\GitHubAppChecks;
use PCIT\GPI\Webhooks\Handler\Skip;
use PCIT\GPI\Webhooks\Handler\Subject;
use PCIT\GPI\Webhooks\Handler\UpdateUserInfo;

class Check
{
    /**
     * completed.
     *
     * requested 用户推送分支
     *
     * rerequested 用户点击了重新运行按钮
     *
     * @throws \Exception
     */
    public function suite(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Check::suite($webhooks_content);

        $installation_id = $context->installation_id;
        $rid = $context->rid;
        $repo_full_name = $context->repo_full_name;
        $branch = $context->branch;
        $commit_id = $context->commit_id;
        $action = $context->action;
        $account = $context->account;
        $check_suite_id = $context->check_suite_id;

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->handle();

        'request' === $action && Build::updateCheckSuiteId((int) $rid, $commit_id, (int) $check_suite_id);
        'rerequested' === $action && Build::updateBuildStatusByCommitId('pending', (int) $rid, $branch, $commit_id);
    }

    /**
     * created updated rerequested requested_action.
     *
     * @throws \Exception
     */
    public function run(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Check::run($webhooks_content);

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

        if (\in_array($action, ['created', 'updated'], true)) {
            return;
        }

        // 用户点击了某一 run 的 re-run
        'rerequested' === $action && Job::updateBuildStatus($external_id, 'pending');

        // 用户点击了按钮，CI 推送修复补丁
        // 'requested_action' === $action &&

        $config = Build::getConfig((int) $external_id);

        $config_array = json_decode($config, true);

        (new Subject())
            ->register(new UpdateUserInfo($account, (int) $installation_id, (int) $rid, $repo_full_name))
            ->register(new Skip(null, (int) $external_id, $branch, $config))
            ->handle();

        if ($config_array) {
            Build::updateBuildStatus((int) $external_id, 'pending');
        }

        GitHubAppChecks::send((int) $external_id);
    }
}

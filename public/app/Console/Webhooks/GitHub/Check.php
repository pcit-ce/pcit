<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Build;
use App\Console\Webhooks\Skip;
use App\Notifications\GitHubAppChecks;
use App\Repo;
use App\User;

class Check
{
    /**
     * completed.
     *
     * requested 用户推送分支
     *
     * rerequested 用户点击了重新运行按钮
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function suite($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'commit_id' => $commit_id,
            'action' => $action,
            'account' => $account,
        ] = \KhsCI\Support\Webhooks\GitHub\Check::suite($json_content);

        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);
        Repo::updateRepoInfo((int) $rid, $repo_full_name, null, null);

        'rerequested' === $action && Build::updateBuildStatusByCommitId('pending', (int) $rid, $branch, $commit_id);
    }

    /**
     * created updated rerequested requested_action.
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function run($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'action' => $action,
            'commit_id' => $commit_id,
            'external_id' => $external_id,
            'check_suite_id' => $check_suite_id,
            'check_run_id' => $check_run_id,
            'branch' => $branch,
            'account' => $account,
        ] = \KhsCI\Support\Webhooks\GitHub\Check::run($json_content);

        if ('rerequested' === $action) {
            // 用户点击了某一 run 的 re-run
            Build::updateBuildStatusByCommitId('pending', (int) $rid, $branch, $commit_id);
        } elseif ('requested_action' === $action) {
            // 用户点击了按钮，CI 推送修复补丁
        } else {
            return;
        }

        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);
        Repo::updateRepoInfo((int) $rid, $repo_full_name, null, null);

        $config = Build::getConfig((int) $external_id);

        $config_array = json_decode($config, true);

        $skip = Skip::handle(null, (int) $external_id, $branch, $config);

        if ($skip) {
            Skip::writeSkipToDB($external_id);

            throw new \Exception('skip', 200);
        }

        if ($config_array) {
            Build::updateBuildStatus((int) $external_id, 'pending');
        }

        GitHubAppChecks::send((int) $external_id);
    }
}

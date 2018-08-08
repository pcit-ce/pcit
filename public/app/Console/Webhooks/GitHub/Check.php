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
            'org' => $org
        ] = \KhsCI\Support\Webhooks\GitHub\Check::suite($json_content);

        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);
        Repo::updateRepoInfo((int) $rid, $repo_full_name, null, null);

        if ('rerequested' === $action) {
            Build::updateBuildStatusByCommitId(
                'pending', (int) $rid, $branch, $commit_id);
        }
    }

    /**
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
            'account_username' => $account_username,
            'account_uid' => $account_uid,
            'account_name' => $account_name,
            'account_email' => $account_email,
            'account_pic' => $account_pic,
            'org' => $org
        ] = \KhsCI\Support\Webhooks\GitHub\Check::run($json_content);

        if ('rerequested' === $action or 'requested_action' === $action) {
            switch ($action) {
                case 'rerequested':
                    // 用户点击了某一 run 的 re-run

                    Build::updateBuildStatusByCommitId(
                        'pending', (int) $rid, $branch, $commit_id);
                    break;

                case 'requested_action':
                    // 用户点击了按钮，CI 推送修复补丁

                    break;
            }
        } else {
            return;
        }

        User::updateUserInfo((int) $account_uid, $account_name, $account_username, $account_email, $account_pic, $org);
        User::updateInstallationId((int) $installation_id, $account_username);
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

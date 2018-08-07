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
            'action' => $action,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'branch' => $branch,
            'commit_id' => $commit_id
        ] = \KhsCI\Support\Webhooks\GitHub\Check::suite($json_content);

        if ('rerequested' === $action) {
            Build::updateBuildStatusByCommitId(
                'pending', 'github', (int) $rid, $branch, $commit_id);
        }

        Repo::updateGitHubInstallationIdByRid('github', (int) $rid, $repo_full_name, (int) $installation_id);
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
            'username' => $username
        ] = \KhsCI\Support\Webhooks\GitHub\Check::run($json_content);

        if ('rerequested' === $action or 'requested_action' === $action) {
            switch ($action) {
                case 'rerequested':
                    // 用户点击了某一 run 的 re-run

                    Build::updateBuildStatusByCommitId(
                        'pending', 'github', (int) $rid, $branch, $commit_id);
                    break;

                case 'requested_action':
                    // 用户点击了按钮，CI 推送修复补丁

                    break;
            }
        } else {
            return;
        }

        Repo::updateGitHubInstallationIdByRid('github', (int) $rid, $repo_full_name, (int) $installation_id);
        User::updateInstallationId('github', (int) $installation_id, $username);

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

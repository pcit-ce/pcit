<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Repo;
use App\User;

class Installation
{
    /**
     * created 用户点击安装按钮.
     *
     * deleted 用户卸载了 GitHub Apps
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function handle($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'action' => $action,
            'repo' => $repositories,
            'sender' => $sender,
            'account' => $account
        ] = \KhsCI\Support\Webhooks\GitHub\Installation::handle($json_content);

        // 仓库管理员信息
        User::updateUserInfo((int) $sender->uid, null, $sender->username, null, $sender->pic);
        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);

        if ('created' === $action) {
            self::create($repositories, $sender->uid);

            return;
        }

        self::delete($installation_id);
    }

    /**
     * 用户首次安装了 GitHub App.
     *
     * @param array $repo
     * @param int   $sender_uid
     *
     * @throws \Exception
     */
    public static function create(array $repo, int $sender_uid): void
    {
        foreach ($repo as $k) {
            // 仓库信息存入 repo 表
            $rid = $k->id;

            $repo_full_name = $k->full_name;

            Repo::updateRepoInfo((int) $rid, $repo_full_name, $sender_uid, null);
        }
    }

    /**
     * 用户卸载了 GitHub App.
     *
     * @param int $installation_id
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function delete(int $installation_id)
    {
        return Repo::deleteByInstallationId($installation_id);
    }

    /**
     * 用户对仓库的操作.
     *
     * added 用户增加仓库
     *
     * removed 移除仓库
     *
     * @param $json_content
     *
     * @throws \Exception
     */
    public static function repositories($json_content): void
    {
        [
            'installation_id' => $installation_id,
            'action' => $action,
            'repo' => $repo,
            'sender' => $sender,
            'account' => $account
        ] = \KhsCI\Support\Webhooks\GitHub\Installation::repositories($json_content);

        User::updateUserInfo((int) $sender->uid, null, $sender->username, null, $sender->pic);
        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);

        if ('added' === $action) {
            self::create($repo, $sender->uid);

            return;
        }

        self::repositories_action_removed($repo);
    }

    /**
     * 用户在设置页面移除了仓库.
     *
     * @param array $repo
     *
     * @throws \Exception
     */
    private static function repositories_action_removed(array $repo): void
    {
        foreach ($repo as $k) {
            $rid = $k->id;

            Repo::deleteByRid((int) $rid);
        }
    }
}

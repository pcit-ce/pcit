<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

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
        ] = \PCIT\GitHub\Webhooks\Parser\Installation::handle($json_content);

        if ('new_permissions_accepted' === $action) {
            \Log::info('receive event [ installation ] action [ new_permissions_accepted ]');

            return;
        }

        if ('deleted' === $action) {
            self::delete($installation_id, $account->username);

            return;
        }

        // 仓库管理员信息
        User::updateUserInfo((int) $sender->uid, null, $sender->username, null, $sender->pic);
        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);
        self::create($repositories, $sender->uid);
    }

    /**
     * 用户首次安装了 GitHub App.
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
     * @throws \Exception
     */
    public static function delete(int $installation_id, string $username): void
    {
        Repo::deleteByInstallationId($installation_id);
        User::updateInstallationId(0, $username);
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
        ] = \PCIT\GitHub\Webhooks\Parser\Installation::repositories($json_content);

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

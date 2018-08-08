<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Repo;
use App\User;

class Installation
{
    /**
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
            'sender_uid' => $sender_uid,
            'sender_username' => $sender_username,
            'sender_pic' => $sender_pic,
            'account' => $account
        ] = \KhsCI\Support\Webhooks\GitHub\Installation::handle($json_content);

        // 仓库管理员信息
        User::updateUserInfo((int) $sender_uid, null, $sender_username, null, $sender_pic);

        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->username);

        if ('created' === $action) {
            self::create($installation_id, $repositories, $sender_uid, $account);

            return;
        }

        self::delete($installation_id);
    }

    /**
     * 用户首次安装了 GitHub App.
     *
     * @param int   $installation_id
     * @param array $repo
     * @param int   $sender_uid
     *
     * @throws \Exception
     */
    public static function create(int $installation_id, array $repo, int $sender_uid): void
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
        return Repo::deleteByInstallationId('github', $installation_id);
    }

    /**
     * 用户对仓库的操作.
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
            'sender_uid' => $sender_uid,
            'sender_username' => $sender_username,
            'sender_pic' => $sender_pic,
            'account' => $account
        ] = \KhsCI\Support\Webhooks\GitHub\Installation::repositories($json_content);

        if ('added' === $action) {
            self::create((int) $installation_id, $repo, $sender_uid);

            return;
        }

        self::repositories_action_removed((int) $installation_id, $repo);
    }

    /**
     * 用户在设置页面移除了仓库.
     *
     * @param int   $installation_id
     * @param array $repo
     *
     * @throws \Exception
     */
    private static function repositories_action_removed(int $installation_id, array $repo): void
    {
        foreach ($repo as $k) {
            $rid = $k->id;

            Repo::deleteByRid('github', (int) $rid, (int) $installation_id);
        }
    }
}

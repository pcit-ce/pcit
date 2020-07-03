<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Repo;
use App\User;

class Installation
{
    public $git_type = 'github';

    /**
     * created 用户点击安装按钮(首次).
     *
     * deleted 用户卸载了 GitHub Apps
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Installation::handle($webhooks_content);

        $installation_id = $context->installation_id;
        $action = $context->action;
        $repositories = $context->repositories;
        $sender = $context->sender;
        $account = $context->account;

        if ('new_permissions_accepted' === $action) {
            \Log::info('receive event [ installation ] action [ new_permissions_accepted ]');

            return;
        }

        if ('deleted' === $action) {
            $this->delete($installation_id, $account->login);

            return;
        }

        // 仓库管理员信息
        User::updateUserInfo((int) $sender->uid, null, $sender->username, null, $sender->pic);
        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->login);
        $this->create($repositories, $sender->uid);
    }

    /**
     * 用户首次安装了 GitHub App.
     *
     * @throws \Exception
     */
    public function create(array $repositories, int $sender_uid): void
    {
        foreach ($repositories as $k) {
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
    public function delete(int $installation_id, string $username): void
    {
        Repo::deleteByInstallationId($installation_id);
        User::updateInstallationId(0, $username);
    }
}

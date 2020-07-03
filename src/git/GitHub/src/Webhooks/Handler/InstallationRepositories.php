<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use App\Repo;
use App\User;

class InstallationRepositories
{
    public $git_type = 'github';

    /**
     * 用户对仓库的操作.
     *
     * added 用户增加仓库
     *
     * removed 移除仓库
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\InstallationRepositories::handle($webhooks_content);

        $installation_id = $context->installation_id;
        $action = $context->action;
        $repositories = $context->repositories;
        $sender = $context->sender;
        $account = $context->account;

        User::updateUserInfo((int) $sender->uid, null, $sender->username, null, $sender->pic);
        User::updateUserInfo($account);
        User::updateInstallationId((int) $installation_id, $account->login);

        if ('added' === $action) {
            $this->added($repositories, $sender->uid);

            return;
        }

        $this->removed($repositories);
    }

    public function added(array $repositories, int $sender_uid): void
    {
        foreach ($repositories as $k) {
            // 仓库信息存入 repo 表
            $rid = $k->id;

            $repo_full_name = $k->full_name;

            Repo::updateRepoInfo((int) $rid, $repo_full_name, $sender_uid, null);
        }
    }

    /**
     * 用户在设置页面移除了仓库.
     *
     * @throws \Exception
     */
    private function removed(array $repositories): void
    {
        foreach ($repositories as $k) {
            $rid = $k->id;

            Repo::deleteByRid((int) $rid);
        }
    }
}

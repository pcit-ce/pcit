<?php

declare(strict_types=1);

namespace PCIT\Pustomize\InstallationRepositories;

use App\Repo;
use App\User;
use PCIT\GPI\Webhooks\Context\InstallationRepositoriesContext;

class Handler
{
    public function handle(InstallationRepositoriesContext $context): void
    {
        $installation_id = $context->installation_id;
        $action = $context->action;
        $repositories = $context->repositories;
        $sender = $context->sender;
        $account = $context->account;
        $git_type = $context->git_type;

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

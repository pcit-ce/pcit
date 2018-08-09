<?php

declare(strict_types=1);

namespace App\Console\Webhooks\GitHub;

use App\Repo;
use App\User;
use KhsCI\Support\Webhooks\GitHub\UserBasicInfo\Account;

/**
 * 每收到 webhooks 更新数据.
 *
 * 观察者模式 观察者
 */
class UpdateUserInfo
{
    private $account;
    private $installation_id;
    private $rid;
    private $repo_full_name;

    public function __construct(Account $account, int $installation_id, $rid, $repo_full_name)
    {
        $this->account = $account;
        $this->installation_id = $installation_id;
        $this->rid = $rid;
        $this->repo_full_name = $repo_full_name;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        User::updateUserInfo($this->account);
        User::updateInstallationId($this->installation_id, $this->account->username);
        Repo::updateRepoInfo($this->rid, $this->repo_full_name, null, null);
    }
}

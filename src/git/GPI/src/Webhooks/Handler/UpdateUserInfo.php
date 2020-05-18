<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use App\Repo;
use App\User;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

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

    private $sender;

    /**
     * UpdateUserInfo constructor.
     *
     * @param $rid
     * @param $repo_full_name
     *
     * @throws \Exception
     */
    public function __construct(Account $account,
                                int $installation_id,
                                $rid,
                                $repo_full_name,
                                Sender $sender = null)
    {
        $this->account = $account;
        $this->installation_id = $installation_id;
        $this->rid = $rid;
        $this->repo_full_name = $repo_full_name;
        $this->sender = $sender->uid ?? null;
        if ($sender) {
            User::updateUserInfo($sender->uid, null, $sender->username);
        }
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        User::updateUserInfo($this->account);
        User::updateInstallationId($this->installation_id, $this->account->username);
        Repo::updateRepoInfo($this->rid, $this->repo_full_name, $this->sender, null);
    }
}

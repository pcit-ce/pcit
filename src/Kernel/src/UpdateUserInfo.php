<?php

declare(strict_types=1);

namespace PCIT;

use App\Repo;
use App\User;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;

/**
 * 每收到 webhooks 更新数据.
 *
 * 观察者模式 观察者
 */
class UpdateUserInfo
{
    /**
     * 项目拥有者 组织账号或个人.
     */
    private $owner;

    private $installation_id;

    private $rid;

    private $repo_full_name;

    private $default_branch;

    private $sender_uid;

    private $git_type;

    private bool $private;

    /**
     * UpdateUserInfo constructor.
     *
     * @param $rid
     */
    public function __construct(
        Owner $owner,
        ?int $installation_id,
        $rid,
        string $repo_full_name,
        ?string $default_branch,
        Sender $sender = null,
        bool $private = false,
        string $git_type = 'github'
    ) {
        $this->owner = $owner;
        $this->installation_id = $installation_id;
        $this->rid = $rid;
        $this->repo_full_name = $repo_full_name;
        $this->sender_uid = $sender->uid ?? null;
        $this->private = $private;
        $this->git_type = $git_type;

        if ($sender) {
            User::updateUserInfo($sender->uid, null, $sender->username, null, null, false, $git_type);
        }
    }

    public function handle(): void
    {
        $git_type = $this->git_type;
        $default_branch = $this->default_branch;
        User::updateUserInfo($this->owner, null, null, null, null, null, $git_type);
        Repo::updateRepoInfo(
            $this->rid,
            $this->repo_full_name,
            $this->sender_uid,
            null,
            $default_branch,
            $this->private,
            $git_type
        );

        if ('github' === $git_type) {
            User::updateInstallationId($this->installation_id, $this->owner->username);
        }
    }
}

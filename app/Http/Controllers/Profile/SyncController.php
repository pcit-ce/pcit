<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\GetAccessToken;
use App\Http\Controllers\Users\JWTController;
use App\Repo;
use App\User;
use PCIT\Framework\Support\DB;
use PCIT\PCIT;

/**
 * 同步用户数据.
 */
class SyncController
{
    /**
     * @var PCIT
     */
    private $pcit;

    private $git_type;

    private $uid;

    private $access_token;

    /**
     * @throws \Exception
     */
    public function __invoke()
    {
        list($this->git_type, $this->uid) = JwtController::getUser();

        $this->access_token = GetAccessToken::getAccessTokenByUid((int) $this->uid, $this->git_type);

        $this->pcit = app(PCIT::class)->setGitType($this->git_type)
        ->setAccessToken($this->access_token);

        if ('github' === $this->git_type) {
            // github 只获取用户组织，不获取用户仓库
            $this->getOrgs();

            return [];
        }

        // sync user basic info
        $this->updateBasicInfo();

        // sync user repos
        $this->getRepo();

        // sync orgs
        $this->getOrgs();

        return [];
    }

    /**
     * sync user basic info.
     *
     * @throws \Exception
     */
    private function updateBasicInfo(): void
    {
        list('uid' => $uid,
            'name' => $name,
            'email' => $email,
            'pic' => $pic
            ) = $this->pcit->user_basic_info->getUserInfo();

        User::updateUserInfo($this->uid, null, $name, $email, $pic, false, $this->git_type);
    }

    /**
     * Sync user repos.
     *
     * @throws \Exception
     */
    private function getRepo(): void
    {
        $page = 0;

        do {
            ++$page;

            $json = $this->pcit->user_basic_info->getRepos($page, false);

            $num_pre_page = \count(json_decode($json));

            $this->parseRepo($json);
        } while ($num_pre_page >= 30);
    }

    /**
     * Sync orgs.
     *
     * @throws \Exception
     */
    private function getOrgs(): void
    {
        DB::beginTransaction();

        if ('github' === $this->git_type) {
            $result = $this->pcit->github_apps_installations->listUser();
            $orgs = json_encode((json_decode($result))->installations);
        } else {
            $orgs = $this->pcit->user_basic_info->listOrgs();
        }

        if (!$orgs) {
            return;
        }

        foreach (json_decode($orgs, true) as $k) {
            if ('github' === $this->git_type) {
                $k = $k['account'];
                $type = $k['type'];

                if ('User' === $type) {
                    continue;
                }
            }
            $org_id = $k['id'];

            $org_name = $k['login'];

            $org_pic = $k['avatar_url'];

            User::updateUserInfo((int) $org_id, null, $org_name, null, $org_pic, true, $this->git_type);

            User::setOrgAdmin((int) $org_id, (int) $this->uid, $this->git_type);
        }

        // check org status

        $orgs = User::getOrgByAdmin($this->uid, $this->git_type);

        foreach ($orgs as $k) {
            $org_name = $k['username'];
            $result = $this->pcit->orgs->exists($org_name);

            if (!$result) {
                User::delete($org_name, $this->git_type);
            }

            if ('github' !== $this->git_type) {
                $this->getOrgsRepo($org_name);
            }
        }

        DB::commit();
    }

    /**
     * Sync orgs repos.
     *
     * @throws \Exception
     */
    private function getOrgsRepo(string $org_name): void
    {
        $page = 0;

        do {
            ++$page;

            $json = $this->pcit->orgs->listRepo($org_name, 1);

            $num_pre_page = \count(json_decode($json));

            $this->parseRepo($json);
        } while ($num_pre_page >= 30);
    }

    /**
     * parse repo json output.
     *
     * @throws \Exception
     */
    private function parseRepo(string $json): void
    {
        DB::beginTransaction();
        if ($obj = json_decode($json)) {
            for ($i = 0; $i < 30; ++$i) {
                $obj_repo = $obj[$i] ?? false;

                if (false === $obj_repo) {
                    break;
                }

                $repo_full_name = $obj_repo->full_name;

                $default_branch = $obj_repo->default_branch;

                /**
                 * 获取 repo 全名，默认分支，是否为管理员（拥有全部权限）.
                 *
                 * gitee permission
                 *
                 * github *s
                 */
                $admin = $obj_repo->permissions->admin ?? $obj_repo->permission->admin ?? null;

                $insert_collaborators = null;
                $insert_admin = null;

                if ($admin) {
                    // uid 是管理员
                    $insert_admin = $this->uid;
                } else {
                    // uid 是协作者
                    $insert_collaborators = $this->uid;
                }

                $rid = $obj_repo->id;

                Repo::updateRepoInfo((int) $rid,
                    $repo_full_name,
                    $insert_admin,
                    $insert_collaborators,
                    $default_branch,
                    $this->git_type
                );
            }
        }
        DB::commit();
    }
}

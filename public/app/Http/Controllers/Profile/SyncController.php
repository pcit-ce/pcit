<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\GetAccessToken;
use App\Http\Controllers\APITokenController;
use App\Repo;
use App\User;
use Exception;
use KhsCI\KhsCI;

class SyncController
{
    /**
     * @var KhsCI
     */
    private $khsci;

    private $git_type;

    private $uid;

    private $access_token;

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        list($this->git_type, $this->uid) = APITokenController::getUser();

        $this->access_token = GetAccessToken::getAccessTokenByUid($this->git_type, (int) $this->uid);

        $this->khsci = new KhsCI(
            [$this->git_type.'_access_token' => $this->access_token], $this->git_type
        );

        // sync user basic info

        $this->getUserBasicInfo();

        // sync user repos

        $this->getRepo();

        // sync orgs

        $this->getOrgs();

        // sync orgs repos

        $this->getOrgsRepo();
    }

    /**
     * sync user basic info
     *
     * @throws Exception
     */
    private function getUserBasicInfo()
    {
        list('uid' => $uid,
            'name' => $name,
            'email' => $email,
            'pic' => $pic
            ) = $this->khsci->user_basic_info->getUserInfo();

        User::updateUserInfo($this->git_type, $this->uid, $name, $email, $pic, $this->access_token);
    }

    /**
     * Sync user repos
     *
     * @throws Exception
     */
    private function getRepo()
    {
        $output = $this->khsci->user_basic_info->getRepos(1, false);

        if ($obj = json_decode($output)) {
            for ($i = 0; $i < 30; ++$i) {
                $obj_repo = $obj[$i] ?? false;

                if (false === $obj_repo) {
                    break;
                }

                $full_name = $obj_repo->full_name ?? false;

                $default_branch = $obj_repo->default_branch;

                /**
                 * 获取 repo 全名，默认分支，是否为管理员（拥有全部权限）.
                 *
                 * gitee permission
                 *
                 * github *s
                 */
                $admin = $obj_repo->permissions->admin ?? $obj_repo->permission->admin ?? null;

                $value = [$full_name, $default_branch, $admin];

                $id = $obj_repo->id;

                $array[$id] = $value;
            }
        }

        Repo::updateRepoInfo();
    }

    /**
     * Sync orgs
     * @throws Exception
     */
    private function getOrgs()
    {
        $orgs = $this->khsci->user_basic_info->listOrgs();

        foreach (json_decode($orgs, true) as $k) {
            $org_id = $k['id'];

            $org_name = $k['login'];

            $org_pic = $k['avatar_url'];

            User::updateUserInfo($this->git_type, (int) $org_id, $org_name, null, $org_pic, null, true);

            User::setOrgAdmin($this->git_type, (int) $org_id, (int) $this->uid);
        }

        // check org status

        $orgs = User::getOrgByAdmin($this->git_type, $this->uid);

        foreach ($orgs as $k) {
            $org_name = $k['username'];
            $output = $this->khsci->github_orgs->exists($org_name);

            if (!$output) {
                User::delete($this->git_type, $org_name);
            }

            $this->getOrgsRepo($org_name);
        }
    }

    /**
     * Sync orgs repos
     */
    private function getOrgsRepo()
    {
        $this->khsci->github_orgs->listRepo($org_name);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\GetAccessToken;
use App\Http\Controllers\APITokenController;
use App\User;
use Exception;
use KhsCI\KhsCI;

class OrganizationsController
{
    /**
     * Returns a list of organizations the current user is a member of.
     *
     * /orgs
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $array = APITokenController::getGitTypeAndUid();

        list('git_type' => $git_type, 'uid' => $uid) = $array[0];

        $khsci = new KhsCI([$git_type.'_access_token' => GetAccessToken::getAccessTokenByUid($git_type, (int) $uid)]);

        $orgs = $khsci->user_basic_info->listOrgs();

        foreach (json_decode($orgs, true) as $k) {
            $org_id = $k['id'];

            $org_name = $k['login'];

            $org_pic = $k['avatar_url'];

            User::updateUserInfo($git_type, (int) $org_id, $org_name, null, $org_pic, null, true);

            User::setOrgAdmin($git_type, (int) $org_id, (int) $uid);
        }

        return User::getOrgByAdmin($git_type, (int) $uid);
    }

    /**
     * Returns an individual organization.
     *
     * /org/{organization_name}
     *
     * @param string $org_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function find(string $org_name)
    {
        $khsci = new KhsCI();

        return $khsci->github_orgs->getBasicInfo($org_name);
    }
}

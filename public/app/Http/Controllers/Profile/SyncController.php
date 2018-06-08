<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\GetAccessToken;
use App\Http\Controllers\APITokenController;
use App\User;
use Exception;
use KhsCI\KhsCI;

class SyncController
{
    /**
     * @throws Exception
     */
    public function __invoke()
    {
        list($git_type, $uid) = APITokenController::getUser();

        $khsci = new KhsCI([$git_type.'_access_token' => GetAccessToken::getAccessTokenByUid($git_type, (int) $uid)]);

        $orgs = $khsci->user_basic_info->listOrgs();

        foreach (json_decode($orgs, true) as $k) {
            $org_id = $k['id'];

            $org_name = $k['login'];

            $org_pic = $k['avatar_url'];

            User::updateUserInfo($git_type, (int) $org_id, $org_name, null, $org_pic, null, true);

            User::setOrgAdmin($git_type, (int) $org_id, (int) $uid);
        }
    }
}

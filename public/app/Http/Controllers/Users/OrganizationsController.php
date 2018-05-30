<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\GetAccessToken;
use App\Http\Controllers\APITokenController;
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

        return $khsci->user_basic_info->listOrgs();
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

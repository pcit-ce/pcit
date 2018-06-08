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
        return User::getOrgByAdmin(...APITokenController::getUser());
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\User;
use Exception;
use PCIT\PCIT;

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
        return User::getOrgByAdmin(...JWTController::getUser(false));
    }

    /**
     * Returns an individual organization.
     *
     * /org/{git_type}/{organization_name}
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function find(string $git_type, string $org_name)
    {
        $pcit = new PCIT();

        return User::getUserInfo($org_name, null, $git_type);
    }
}

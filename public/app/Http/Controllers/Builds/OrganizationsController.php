<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\APITokenController;

class OrganizationsController
{
    /**
     * Returns a list of organizations the current user is a member of.
     *
     * /orgs
     *
     * @throws \Exception
     */
    public function __invoke(): void
    {
        $array = APITokenController::getGitTypeAndUid();

        list('git_type' => $git_type, 'uid' => $uid) = $array[0];
    }

    /**
     * Returns an individual organization.
     *
     * /org/{organization_name}
     *
     * @param string $org_name
     */
    public function find(string $org_name): void
    {
    }
}

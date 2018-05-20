<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;


class OrganizationsController
{
    /**
     * Returns a list of organizations the current user is a member of.
     *
     * /orgs
     */
    public function __invoke(): void
    {

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

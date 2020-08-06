<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use PCIT\GPI\ServiceClientCommon;

class BranchesClient
{
    use ServiceClientCommon;

    public function list(string $repo_full_name, bool $protected): void
    {
    }

    /**
     * @see https://developer.github.com/v3/repos/branches/#get-branch
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get(string $username, string $repo_name, string $branch)
    {
        $url = $this->api_url.'/repos/'.$username.'/'.$repo_name.'/branches/'.$branch;

        return $this->curl->get($url);
    }

    public function getBranchProtection(): void
    {
    }

    public function updateBranchProtection(): void
    {
    }

    public function removeBranchProtection(): void
    {
    }

    public function getRequiredStatusChecks(): void
    {
    }

    public function updateRequiredStatusChecks(): void
    {
    }

    public function removeRequiredStatusChecks(): void
    {
    }

    public function listRequiredStatusChecksContexts(): void
    {
    }

    public function replaceRequiredStatusChecksContexts(): void
    {
    }

    public function addRequiredStatusChecksContexts(): void
    {
    }

    public function removeRequiredStatusChecksContexts(): void
    {
    }

    public function getPullRequestReviewEnforcement(): void
    {
    }

    public function updatePullRequestReviewEnforcement(): void
    {
    }

    public function removePullRequestReviewEnforcement(): void
    {
    }

    public function getAdminEnforcement(): void
    {
    }

    public function addAdminEnforcement(): void
    {
    }

    public function removeAdminEnforcement(): void
    {
    }

    public function getRestrictions(): void
    {
    }

    public function removeRestrictions(): void
    {
    }

    public function listTeamRestrictions(): void
    {
    }

    public function replaceTeamRestrictions(): void
    {
    }

    public function addTeamRestrictions(): void
    {
    }

    public function removeTeamRestrictions(): void
    {
    }

    public function listUserRestrictions(): void
    {
    }

    public function replaceUserRestrictions(): void
    {
    }

    public function addUserRestrictions(): void
    {
    }

    public function removeUserRestrictions(): void
    {
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Organizations;

use Exception;
use KhsCI\Service\CICommon;

class GitHubClient
{
    use CICommon;

    /**
     * 获取组织的基本信息.
     *
     * @param string $org_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getBasicInfo(string $org_name)
    {
        $url = $this->api_url.'/orgs'.$org_name;

        return $this->curl->get($url);
    }

    /**
     * 获取组织的仓库列表.
     *
     * @param string $org_name
     * @param int    $page
     * @param string $type
     * @param int    $per_page
     * @param string $sort
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listRepo(string $org_name, int $page = 1, string $type = null, int $per_page = null, string $sort = null)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/repos';

        return $this->curl->get($url);
    }

    /**
     * List blocked users
     *
     * @param string $org_name
     */
    public function listBlockedUsers(string $org_name)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/blocks';
    }

    /**
     * Check whether a user is blocked from an organization
     *
     * @param string $org_name
     * @param string $username
     *
     * @return bool
     * @throws Exception
     */
    public function isBlockedUser(string $org_name, string $username)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/blocks/'.$username;

        $this->curl->get($url);

        $http_return_code = $this->curl->getCode();

        if (204 === $http_return_code) {

            return true;
        }

        if (404 === $http_return_code) {

            return false;
        }

        throw new Exception('Error', 500);
    }

    /**
     * Block a user
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @return mixed
     * @throws Exception
     */
    public function blockUser(string $org_name, string $username)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/blocks/'.$username;

        return $this->curl->put($url);
    }

    /**
     * Unblock a user
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @return mixed
     * @throws Exception
     */
    public function unblockUser(string $org_name, string $username)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/blocks/'.$username;

        return $this->curl->delete($url);
    }
}

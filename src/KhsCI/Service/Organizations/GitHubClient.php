<?php


namespace KhsCI\Service\Organizations;

use Exception;
use KhsCI\Service\CICommon;

class GitHubClient
{
    use CICommon;

    /**
     * 获取组织的基本信息
     *
     * @param string $org_name
     *
     * @return mixed
     * @throws Exception
     */
    public function getBasicInfo(string $org_name)
    {
        $url = self::$api_url.'/orgs'.$org_name;

        return self::$curl->get($url);
    }

    /**
     * 获取组织的仓库列表
     *
     * @param string $org_name
     * @param int    $page
     * @param string $type
     * @param int    $per_page
     * @param string $sort
     *
     * @return mixed
     * @throws Exception
     */
    public function listRepo(string $org_name, int $page = 1, string $type = null, int $per_page = null, string $sort = null)
    {
        $url = self::$api_url.'/orgs/'.$org_name.'/repos';

        return self::$curl->get($url);
    }
}

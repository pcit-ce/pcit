<?php

declare(strict_types=1);

namespace KhsCI\Service\Users;

use Curl\Curl;
use Exception;

class GitHubClient
{
    /**
     * @var null|string
     */
    private static $api_url = null;

    /**
     * @var Curl
     */
    private static $curl;

    public function __construct(Curl $curl, string $api_url)
    {
        self::$curl = $curl;
        self::$api_url = $api_url;
    }

    /**
     * 获取当前登录用户或指定用户的基本信息.
     *
     * @param bool        $raw
     * @param string|null $username
     *
     * @return array|mixed
     *
     * @throws Exception
     */
    public function getUserInfo(bool $raw = false, string $username = null)
    {
        $url = self::$api_url.'/user';

        if ($username) {
            $url = self::$api_url.'/users/'.$username;
        }

        $json = self::$curl->get($url);

        if ($raw) {
            return $json;
        }

        $obj = json_decode($json);

        return [
            'uid' => $obj->id,
            'name' => $obj->login,
            'email' => $obj->email,
            'pic' => $obj->avatar_url,
        ];
    }

    /**
     * 获取当前用户或指定用户名下所有的仓库列表（包括组织中的列表）.
     *
     * @param int    $page
     * @param bool   $raw
     * @param string $username
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getRepos(int $page = 1, bool $raw = false, string $username)
    {
        $url = self::$api_url.'/user/repos?page='.$page;

        if ($username) {
            $url = self::$api_url.'/users/'.$username.'/repos?page='.$page;
        }

        $output = self::$curl->get($url);

        return $output;
    }

    /**
     * 获取用户名下组织列表.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listOrgs()
    {
        $url = self::$api_url.'/user/orgs';

        self::$curl->get($url);

        var_dump(self::$curl->getRequestHeaders());
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function authorizations(string $username, string $password)
    {
        $url = self::$api_url.'/authorizations';

        self::$curl->setHtpasswd($username, $password);

        return self::$curl->get($url);
    }
}

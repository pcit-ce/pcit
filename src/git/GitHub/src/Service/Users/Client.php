<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Users;

use Curl\Curl;
use Exception;

class Client
{
    protected $api_url;

    /**
     * @var Curl
     */
    protected $curl;

    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;
        $this->api_url = $api_url;
    }

    /**
     * 获取当前登录用户或指定用户的基本信息.
     *
     * @throws \Exception
     *
     * @return array|string
     */
    public function getUserInfo(bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/user';

        if ($username) {
            $url = $this->api_url.'/users/'.$username;
        }

        $json = $this->curl->get($url);

        if (200 !== $this->curl->getCode()) {
            throw new \Exception($json, $this->curl->getCode());
        }

        if ($raw) {
            return $json;
        }

        $obj = json_decode($json);

        return [
            'uid' => $obj->id,
            'name' => $obj->login,
            'email' => $obj->email ?? null,
            'pic' => $obj->avatar_url,
        ];
    }

    /**
     * 获取当前用户或指定用户名下所有的仓库列表（包括组织中的列表）.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getRepos(int $page = 1, bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/user/repos?page='.$page;

        if ($username) {
            $url = $this->api_url.'/users/'.$username.'/repos?page='.$page;
        }

        return $this->curl->get($url);
    }

    /**
     * 获取用户名下组织列表.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function listOrgs()
    {
        $url = $this->api_url.'/user/orgs';

        $output = $this->curl->get($url);

        $http_return_code = $this->curl->getCode();

        if (200 !== $http_return_code) {
            throw new Exception($output, $http_return_code);
        }

        return $output;
    }

    public function listBlockedUsers(): void
    {
    }

    public function userIsBlocked(): void
    {
    }

    public function blockUser(): void
    {
    }

    public function UnblockUser(): void
    {
    }
}

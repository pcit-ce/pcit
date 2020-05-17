<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Users;

use Exception;
use PCIT\GitHub\Service\Users\Client as GitHubClient;
use PCIT\GPI\ServiceClientCommon;

class Client extends GitHubClient
{
    use ServiceClientCommon;

    public function getAccessToken()
    {
        return '?access_token='.explode(' ', $this->curl->headers['x-coding-token'])[1];
    }

    /**
     * @param string $username
     *
     * @return array|string
     *
     * @throws \Exception
     */
    public function getUserInfo(bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/account/current_user';

        if ($username) {
            $url = $this->api_url.'/account/key'.$username;
        }

        $json = $this->curl->get($url.$this->getAccessToken());

        if ($raw) {
            return $json;
        }

        $obj = json_decode($json);

        if (0 !== $obj->code) {
            throw new Exception('Coding login error, '.$obj->code, 500);
        }

        $obj = $obj->data;

        return [
            'uid' => $obj->id,
            'name' => $obj->name,
            'email' => $obj->email ?? 'null',
            'pic' => $obj->avatar,
        ];
    }

    /**
     * 这里指项目中的仓库，个人用户不包含仓库。
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getRepos(int $page = 1, bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/user/projects';

        return '[]';

        if ($username) {
            $url = $this->api_url.'/api/user/'.$username.'/projects/public';
        }

        $json = $this->curl->get($url.$this->getAccessToken());

        $json_obj = json_decode($json);

        if (0 !== $json_obj->code) {
            \Log::debug('Coding user repo not found');

            throw new Exception('Not Found', 404);
        }

        $repo = (array) $json_obj->data->list;

        $array = [];

        foreach ($repo as $k) {
            $id = $k->id;
            $repo_path = explode('/', $k->backend_project_path);
            $full_name = $repo_path[2].'/'.$repo_path[4];
            $default_branch = '';

            $array[] = [
                'id' => $id,
                'full_name' => $full_name,
                'default_branch' => $default_branch,
                'permissions' => ['admin' => true],
            ];
        }

        if ($raw) {
            return $json;
        }

        return json_encode($array);
    }

    /**
     * 这里指项目.
     */
    public function listOrgs()
    {
        $url = $this->api_url.'/user/projects';

        $result = $this->curl->get($url.$this->getAccessToken());

        $orgs = json_decode($result)->data->list;

        $orgs_array = [];

        foreach ($orgs as $org) {
            $icon = $org->icon;
            if (false === strrpos($icon, 'https')) {
                $icon = 'https://dn-coding-net-production-pp.codehub.cn/79a8bcc4-d9cc-4061-940d-5b3bb31bf571.png';
            }

            $orgs_array[] = [
                'id' => $org->id,
                'login' => $org->default_depot_name,
                'avatar_url' => $icon,
            ];
        }

        return json_encode($orgs_array);
    }
}

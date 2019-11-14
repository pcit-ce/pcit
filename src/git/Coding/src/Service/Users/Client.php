<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Users;

use Exception;
use PCIT\Framework\Support\Log;
use PCIT\GitHub\Service\CICommon;
use PCIT\GitHub\Service\Users\Client as GitHubClient;

class Client extends GitHubClient
{
    use CICommon;

    /**
     * @param string $username
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function getUserInfo(bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/account/current_user';

        if ($username) {
            $url = $this->api_url.'/account/key'.$username;
        }

        $json = $this->curl->get($url);

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
            'name' => $obj->global_key,
            'email' => $obj->email ?? 'null',
            'pic' => $obj->avatar,
        ];
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getRepos(int $page = 1, bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/user/projects';

        if ($username) {
            $url = $this->api_url.'/api/user/'.$username.'/projects/public';
        }

        $json = $this->curl->get($url);

        $json_obj = json_decode($json);

        if (0 !== $json_obj->code) {
            Log::debug(__FILE__, __LINE__, 'Coding user repo not found');

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

    public function listOrgs()
    {
        return [];
    }
}

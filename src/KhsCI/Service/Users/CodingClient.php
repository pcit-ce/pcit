<?php

namespace KhsCI\Service\Users;

use Exception;
use KhsCI\Service\CICommon;

class CodingClient extends GitHubClient
{
    use CICommon;

    /**
     * @param bool   $raw
     * @param string $username
     *
     * @return array|string
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
     * @param int         $page
     * @param bool        $raw
     * @param string|null $username
     *
     * @return mixed
     * @throws Exception
     */
    public function getRepos(int $page = 1, bool $raw = false, string $username = null)
    {
        $url = $this->api_url.'/user/projects';

        if ($username) {
            $url = $this->api_url.'/api/user/'.$username.'/projects/public';
        }

        $json = $this->curl->get($url);

        if ($raw) {
            return $json;
        }


    }
}

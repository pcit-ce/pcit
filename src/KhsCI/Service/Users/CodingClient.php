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

        $array = json_decode($json, true);

        return [

        ];
    }
}

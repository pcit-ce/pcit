<?php

namespace KhsCI\Service\Users;

use Curl\Curl;
use Exception;

class BasicInfo
{
    private static $api_url = null;

    private static $curl;

    public function __construct(Curl $curl)
    {
        self::$curl = $curl;
        self::$api_url = 'https://api.github.com';
    }

    /**
     * @param bool $raw
     *
     * @return array|mixed
     * @throws Exception
     */
    public function getUserInfo(bool $raw = false)
    {
        $url = self::$api_url.'/user';

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
     * @param int  $page
     * @param bool $raw
     *
     * @return mixed
     * @throws Exception
     */
    public function getProjects(int $page = 1, bool $raw = false)
    {
        $url = self::$api_url.'/user/repos?page='.$page;

        $output = self::$curl->get($url);

        return $output;
    }
}

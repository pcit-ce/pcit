<?php

declare(strict_types=1);

namespace KhsCI\Service\Users;

use Curl\Curl;
use Exception;

class BasicInfo
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
     * @param bool $raw
     *
     * @return array|mixed
     *
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
     *
     * @throws Exception
     */
    public function getRepos(int $page = 1, bool $raw = false)
    {
        $url = self::$api_url.'/user/repos?page='.$page;

        $output = self::$curl->get($url);

        return $output;
    }
}

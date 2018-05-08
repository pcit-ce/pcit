<?php

namespace KhsCI\Service\GitHubApps;

use Curl\Curl;

class Installations
{
    const API_URL = 'https://api.github.com';

    private static $curl;

    public function __construct(Curl $curl)
    {
        self::$curl = $curl;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function list()
    {
        $url = self::API_URL.'/user/installations';

        return self::$curl->get($url);
    }
}

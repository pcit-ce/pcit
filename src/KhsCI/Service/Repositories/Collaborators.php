<?php

namespace KhsCI\Service\Repositories;

use Curl\Curl;

class Collaborators
{
    private static $curl;

    public function __construct(Curl $curl)
    {
        self::$curl = $curl;
    }

    /**
     * @param string $git_type
     * @param string $repo_full_name
     *
     * @return mixed
     * @throws \Exception
     */
    public function list(string $git_type, string $repo_full_name)
    {
        $url = '';

        return self::$curl->get($url);
    }
}

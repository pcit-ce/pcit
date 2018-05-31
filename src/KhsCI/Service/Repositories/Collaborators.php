<?php

declare(strict_types=1);

namespace KhsCI\Service\Repositories;

use Curl\Curl;
use Exception;

class Collaborators
{
    private $curl;

    private $api_url;

    /**
     * Collaborators constructor.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/collaborators';

        return $this->curl->get($url);
    }
}

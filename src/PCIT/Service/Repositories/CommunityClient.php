<?php

declare(strict_types=1);

namespace PCIT\Service\GitHub\Repositories;

use PCIT\Service\CICommon;

class CommunityClient
{
    use CICommon;

    /**
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws \Exception
     *
     * @see https://developer.github.com/v3/repos/community/
     */
    public function retrieve(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/community/profile';

        return $this->curl->get($url, [], ['Accept' => 'application/vnd.github.machine-man-preview+json;
        application/vnd.github.speedy-preview+json;
        application/vnd.github.black-panther-preview+json']);
    }
}

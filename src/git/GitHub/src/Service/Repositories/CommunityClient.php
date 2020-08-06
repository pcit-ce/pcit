<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use PCIT\GPI\ServiceClientCommon;

class CommunityClient
{
    use ServiceClientCommon;

    /**
     * @throws \Exception
     *
     * @return mixed
     *
     * @see https://developer.github.com/v3/repos/community/
     */
    public function retrieve(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/community/profile';

        return $this->curl->get($url, [], ['Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.black-panther-preview+json']);
    }
}

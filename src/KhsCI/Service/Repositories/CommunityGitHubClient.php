<?php

declare(strict_types=1);

namespace KhsCI\Service\Repositories;

use KhsCI\Service\CICommon;

class CommunityGitHubClient
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

        return $this->curl->get($url, [], ['Accept' => 'application/vnd.github.machine-man-preview.speedy-preview.black-panther-preview+json']);
    }
}

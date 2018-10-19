<?php

declare(strict_types=1);

namespace PCIT\Service\Repositories;

use PCIT\Service\CICommon;

class MergingGitHubClient
{
    use CICommon;

    /**
     * Perform a merge.
     *
     * 201
     *
     * @param string $repo_full_name
     * @param string $base
     * @param string $head
     * @param string $commit_message
     *
     * @return mixed
     *
     * @throws \Exception
     *
     * @see https://developer.github.com/v3/repos/merging/#perform-a-merge
     */
    public function merge(string $repo_full_name, string $base, string $head, string $commit_message)
    {
        $data = [
            'base' => $base,
            'head' => $head,
            'commit_message' => $commit_message,
        ];

        $url = $this->api_url.'/repos/'.$repo_full_name.'/merges';

        return $this->curl->post($url, json_encode($data));
    }
}

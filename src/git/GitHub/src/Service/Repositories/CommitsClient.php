<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use PCIT\GitHub\Service\CICommon;

class CommitsClient
{
    use CICommon;

    /**
     * List commits on a repository.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function list(string $repo_full_name,
                         string $sha,
                         string $path,
                         string $author,
                         string $since,
                         string $until)
    {
        $data = [
            'sha' => $sha,
            'path' => $path,
            'author' => $author,
            'since' => $since,
            'until' => $until,
        ];

        $url = $this->api_url.'/repos/'.$repo_full_name.'/commits?'.http_build_query($data);

        return $this->curl->get($url);
    }

    /**
     * Get a single commit.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function get(string $repo_full_name, string $sha)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/commits/'.$sha;

        return $this->curl->get($url);
    }

    /**
     * Get the SHA-1 of a commit reference.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getShaOfRef(string $repo_full_name, string $ref)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/commits/'.$ref;

        return $this->curl->get($url, [], ['Accept' => 'application/vnd.github.v3.sha']);
    }

    /**
     * Compare two commits.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function compare(string $repo_full_name, string $base, string $head)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/compare/'.$base.'...'.$head;

        return $this->curl->get($url);
    }

    /**
     * Commit signature verification.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function SignatureVerification(string $repo_full_name, string $sha)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/commits/'.$sha;

        return $this->curl->get($url);
    }
}

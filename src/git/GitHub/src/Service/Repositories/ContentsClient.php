<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use Exception;
use PCIT\GPI\Service\Repositories\ContentsClientInterface;
use PCIT\GPI\ServiceClientCommon;

class ContentsClient implements ContentsClientInterface
{
    use ServiceClientCommon;

    /**
     * Get the README.
     *
     * @throws \Exception
     *
     * @return mixed
     *
     * @aee https://developer.github.com/v3/repos/contents/#get-the-readme
     */
    public function getReadme(string $repo_full_name, string $ref)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/readme?'.http_build_query(['ref' => $ref]));
    }

    /**
     * Get contents.
     *
     * @throws \Exception
     */
    public function getContents(string $repo_full_name, string $path, string $ref, bool $raw = true): string
    {
        $headers = [];
        if ($raw) {
            $headers = [
                'Accept' => 'application/vnd.github.3.raw',
            ];
        }

        $result = $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/contents/'.$path.'?'.http_build_query(['ref' => $ref]), null, $headers);

        if (200 !== $this->curl->getCode()) {
            throw new Exception('http code is not 200');
        }

        return $result;
    }

    /**
     * Create a file.
     *
     * @param string $branch
     * @param string $committer_name
     * @param string $committer_email
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function createFile(
        string $repo_full_name,
        string $path,
        string $commit_message,
        string $content,
        ?string $branch,
        ?string $committer_name,
        ?string $committer_email
    )
    {
        $data = [
            'message' => $commit_message,
            'content' => $content,
            'branch' => $branch,
        ];

        if ($committer_name) {
            $data = array_merge(
                $data,
                ['committer' => [
                    'name' => $committer_name,
                    'email' => $committer_email,
                ],
                ]
            );
        }

        $url = $this->api_url.'/repos/'.$repo_full_name.'/contents/'.$path;

        return $this->curl->put($url, json_encode(array_filter($data)));
    }

    /**
     * Update a file.
     *
     * @param string $branch
     * @param string $committer_name
     * @param string $committer_email
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function updateFile(
        string $repo_full_name,
        string $path,
        string $commit_message,
        string $content,
        string $sha,
        ?string $branch,
        ?string $committer_name,
        ?string $committer_email
    )
    {
        $data = [
            'message' => $commit_message,
            'content' => $content,
            'sha' => $sha,
            'branch' => $branch,
        ];

        if ($committer_name) {
            $data = array_merge(
                $data,
                ['committer' => [
                    'name' => $committer_name,
                    'email' => $committer_email,
                ],
                ]
            );
        }

        $url = $this->api_url.'/repos/'.$repo_full_name.'/contents/'.$path;

        return $this->curl->put($url, json_encode(array_filter($data)));
    }

    /**
     * Delete a file.
     *
     * @param string $branch
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function deleteFile(
        string $repo_full_name,
        string $path,
        string $commit_message,
        string $sha,
        ?string $branch,
        string $committer_name,
        string $committer_email
    )
    {
        $data = [
            'message' => $commit_message,
            'sha' => $sha,
            'branch' => $branch,
        ];

        if ($committer_name) {
            $data = array_merge(
                $data,
                ['committer' => [
                    'name' => $committer_name,
                    'email' => $committer_email,
                ],
                ]
            );
        }

        $url = $this->api_url.'/repos/'.$repo_full_name.'/contents/'.$path;

        return $this->curl->delete($url, json_encode(array_filter($data)));
    }

    /**
     * Get archive link.
     *
     * @param string $archive_format tarball or zipball
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getArchiveLink(string $repo_full_name, string $ref, string $archive_format = 'tarball')
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/'.$archive_format.'/'.$ref;

        return $this->curl->get($url);
    }
}

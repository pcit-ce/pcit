<?php

declare(strict_types=1);

namespace PCIT\Service\GitHub\Repositories;

use PCIT\Service\CICommon;

class ContentsClient
{
    use CICommon;

    /**
     * Get the README.
     *
     * @param string $repo_full_name
     * @param string $ref
     *
     * @return mixed
     *
     * @throws \Exception
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
     * @param string $repo_full_name
     * @param string $path
     * @param string $ref
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function getContents(string $repo_full_name, string $path, string $ref)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/contents/'.$path.'?'.http_build_query(['ref' => $ref]));
    }

    /**
     * Create a file.
     *
     * @param string $repo_full_name
     * @param string $path
     * @param string $commit_message
     * @param string $content
     * @param string $branch
     * @param string $committer_name
     * @param string $committer_email
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function createFile(string $repo_full_name,
                               string $path,
                               string $commit_message,
                               string $content,
                               ?string $branch,
                               ?string $committer_name,
                               ?string $committer_email)
    {
        $data = [
            'message' => $commit_message,
            'content' => $content,
            'branch' => $branch,
        ];

        if ($committer_name) {
            $data = array_merge($data, ['committer' => [
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
     * @param string $repo_full_name
     * @param string $path
     * @param string $commit_message
     * @param string $content
     * @param string $sha
     * @param string $branch
     * @param string $committer_name
     * @param string $committer_email
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function updateFile(string $repo_full_name,
                               string $path,
                               string $commit_message,
                               string $content,
                               string $sha,
                               ?string $branch,
                               ?string $committer_name,
                               ?string $committer_email)
    {
        $data = [
            'message' => $commit_message,
            'content' => $content,
            'sha' => $sha,
            'branch' => $branch,
        ];

        if ($committer_name) {
            $data = array_merge($data, ['committer' => [
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
     * @param string $repo_full_name
     * @param string $path
     * @param string $commit_message
     * @param string $sha
     * @param string $branch
     * @param string $committer_name
     * @param string $committer_email
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function deleteFile(string $repo_full_name,
                               string $path,
                               string $commit_message,
                               string $sha,
                               ?string $branch,
                               string $committer_name,
                               string $committer_email)
    {
        $data = [
            'message' => $commit_message,
            'sha' => $sha,
            'branch' => $branch,
        ];

        if ($committer_name) {
            $data = array_merge($data, ['committer' => [
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
     * @param string $repo_full_name
     * @param string $archive_format tarball or zipball
     * @param string $ref
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getArchiveLink(string $repo_full_name, string $archive_format = 'tarball', string $ref)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/'.$archive_format.'/'.$ref;

        return $this->curl->get($url);
    }
}

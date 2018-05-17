<?php

namespace KhsCI\Service\Repositories;

use Exception;
use KhsCI\Service\CICommon;

/**
 * Class Releases
 * @see https://developer.github.com/v3/repos/releases/
 */
class Releases
{
    use CICommon;

    /**
     * @param string $repo_full_name
     *
     * @return mixed
     * @throws Exception
     */
    public function list(string $repo_full_name)
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/releases';

        return self::$curl->get($url);
    }

    /**
     * Get a single release
     *
     * @param string $repo_full_name
     * @param int    $release_id
     *
     * @return mixed
     * @throws Exception
     */
    public function get(string $repo_full_name, int $release_id)
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/releases/'.$release_id;

        return self::$curl->get($url);
    }

    public function latest()
    {

    }

    public function getByTag()
    {

    }

    /**
     * 201
     *
     * @param string $repo_full_name
     * @param string $tag_name
     * @param string $target_commitish Specifies the commitish value that determines where the Git tag is created from.
     *                                 Can be any branch or commit SHA. Unused if the Git tag already exists. Default:
     *                                 the repository's default branch (usually master)
     * @param string $name
     * @param string $body
     * @param bool   $draft
     * @param bool   $prerelease
     *
     * @throws Exception
     */
    public function create(string $repo_full_name,
                           string $tag_name,
                           string $target_commitish,
                           string $name,
                           string $body,
                           bool $draft = false,
                           bool $prerelease = false): void
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/releases';

        $data = [
            'tag_name' => $tag_name,
            'target_commitish' => $target_commitish,
            'name' => $name,
            'body' => $body,
            'draft' => $draft,
            'preleases' => $prerelease
        ];

        self::$curl->post($url, json_encode($data));

        self::successOrFailure(__FILE__, __LINE__, 201);
    }

    public function edit()
    {

    }

    public function delete()
    {

    }

    public function listAssets()
    {

    }

    public function uploadAssets(string $content_type, string $name, string $label)
    {

    }

    public function getAssets()
    {

    }

    public function editAssets()
    {

    }

    public function deleteAssets()
    {

    }
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use PCIT\GPI\ServiceClientCommon;

/**
 * Class Releases.
 *
 * @see https://developer.github.com/v3/repos/releases/
 */
class ReleasesClient
{
    use ServiceClientCommon;

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function list(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases';

        return $this->curl->get($url);
    }

    /**
     * Get a single release.
     *
     * @param int    $release_id
     * @param string $tag_name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get(string $repo_full_name, ?int $release_id, ?string $tag_name)
    {
        if ($tag_name) {
            $release_id = json_decode($this->getByTag($repo_full_name, $tag_name))->id;
        }

        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/'.$release_id;

        return $this->curl->get($url);
    }

    /**
     * Get the latest release.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function latest(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/latest';

        return $this->curl->get($url);
    }

    /**
     * Get a release by tag name.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getByTag(string $repo_full_name, string $tag_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/tags/'.$tag_name;

        return $this->curl->get($url);
    }

    /**
     * 201.
     *
     * @param string $target_commitish Specifies the commitish value that determines where the Git tag is created from.
     *                                 Can be any branch or commit SHA. Unused if the Git tag already exists. Default:
     *                                 the repository's default branch (usually master)
     * @param string $method
     *
     * @throws \Exception
     */
    public function create(
        string $repo_full_name,
        string $tag_name,
        string $target_commitish,
        string $name,
        string $body,
        bool $draft = false,
        bool $prerelease = false,
        $method = 'post'
    ): void {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases';

        $data = [
            'tag_name' => $tag_name,
            'target_commitish' => $target_commitish,
            'name' => $name,
            'body' => $body,
            'draft' => $draft,
            'preleases' => $prerelease,
        ];

        $this->curl->$method($url, json_encode($data));

        $this->successOrFailure(201);
    }

    /**
     * Edit a release.
     *
     * @param string $method
     *
     * @throws \Exception
     */
    public function edit(
        string $repo_full_name,
        string $tag_name,
        string $target_commitish,
        string $name,
        string $body,
        bool $draft = false,
        bool $prerelease = false,
        $method = 'patch'
    ): void {
        $this->create(...\func_get_args());
    }

    /**
     * Delete a release.
     *
     * @param int    $release_id
     * @param string $tag_name
     *
     * @throws \Exception
     */
    public function delete(string $repo_full_name, ?int $release_id, ?string $tag_name): void
    {
        if ($tag_name) {
            $release_id = json_decode($this->getByTag($repo_full_name, $tag_name))->id;
        }

        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/'.$release_id;

        $this->curl->delete($url);
    }

    /**
     * List assets for a release.
     *
     * @param int    $release_id
     * @param string $tag_name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function listAssets(string $repo_full_name, ?int $release_id, ?string $tag_name)
    {
        if ($tag_name) {
            $release_id = json_decode($this->getByTag($repo_full_name, $tag_name))->id;
        }

        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/'.$release_id.'/assets';

        return $this->curl->get($url);
    }

    /**
     * Upload a release asset.
     *
     * 201
     *
     * @param        $file_content
     * @param int    $release_id
     * @param string $tag_name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function uploadAsset(
        string $repo_full_name,
        $file_content,
        ?int $release_id,
        ?string $tag_name,
        string $name,
        ?string $label,
        string $content_type = 'application/octet-stream',
        bool $replace = false
    ) {
        if ($tag_name) {
            $release_id = json_decode($this->getByTag($repo_full_name, $tag_name))->id;
        }

        if ($replace) {
            $asset = $this->listAssets($repo_full_name, $release_id, null);

            foreach (json_decode($asset) as $k) {
                if ($label === $k->label) {
                    $this->deleteAsset($repo_full_name, $k->id);

                    break;
                }
            }
        }

        $data = [
            'name' => $name,
            'label' => $label,
        ];

        $url = 'https://uploads.github.com/repos/'.$repo_full_name.'/releases/'.$release_id.'/assets?'.http_build_query($data);

        return $this->curl->post($url, $file_content, ['Content-Type' => $content_type]);
    }

    /**
     * Get a single release asset.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getAsset(string $repo_full_name, int $asset_id)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/assets/'.$asset_id;

        return $this->curl->get($url);
    }

    /**
     * Edit a release asset.
     *
     * @throws \Exception
     */
    public function editAsset(string $repo_full_name, string $name, string $label, int $asset_id): void
    {
        $data = [
            'name' => $name,
            'label' => $label,
        ];

        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/assets/'.$asset_id;

        $this->curl->patch($url, json_encode($data));
    }

    /**
     * Delete a release asset.
     *
     * 204
     *
     * @throws \Exception
     */
    public function deleteAsset(string $repo_full_name, int $asset_id): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/releases/assets/'.$asset_id;

        $this->curl->delete($url);
    }
}

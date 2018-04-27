<?php

namespace KhsCI\Service\Deployments;

use KhsCI\Support\HTTP;

/**
 *
 * 展示 CI 状态
 *
 * @see https://developer.github.com/v3/repos/deployments/
 */
class GitHubController
{
    const API_URL = 'https://api.github.com';

    public $username;

    public $repo;

    public $urlCommon;

    public function __construct($username, $repo)
    {
        $this->username = $username;
        $this->repo = $repo;
        $this->urlCommon = [self::API_URL, 'repos', $this->username, $this->repo, 'deployments'];
    }

    /**
     * List deployments.
     *
     * @param string $sha The SHA that was recorded at creation time. Default: <code>none<code>
     * @param string $ref The name of the ref. This can be a branch, tag, or SHA. Default: <code>none<code>
     * @param string $task The name of the task for the deployment (e.g., <code>deploy<code> or <code>deploy:migrations<code>). Default: <code>none<code>
     * @param string $environment The name of the environment that was deployed to (e.g., <code>staging<code> or <code>production<code>). Default: <code>none<code>
     * @return mixed
     */
    public function list(string $sha, string $ref, string $task, string $environment)
    {
        $queryParameters = http_build_query([
            'sha' => $sha,
            'ref' => $ref,
            'task' => $task,
            'environment' => $environment,
        ]);

        $url = implode('/', $this->urlCommon);

        return HTTP::get($url.'?'.$queryParameters, null);

    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getSingleInfo(string $id)
    {
        $urlCommon = $this->urlCommon;

        array_push($urlCommon, $id);

        $url = implode('/', $urlCommon);

        return HTTP::get($url);
    }

    /**
     * Create a deployment
     *
     * @param string $ref
     * @param string $task
     * @param bool $auto_merge
     * @param array|null $required_contexts
     * @param string|null $payload
     * @param string $environment
     * @param string|null $description
     * @param bool|null $transient_environment
     * @param bool|null $production_environment
     */
    public function create(string $ref,
                           string $task = 'deploy',
                           bool $auto_merge = true,
                           array $required_contexts = null,
                           string $payload = null,
                           string $environment = 'production',
                           string $description = null,
                           bool $transient_environment = null,
                           bool $production_environment = null

    )
    {

        $array = [
            'ref' => $ref,
            'task' => $task,
            'auto_merge' => $auto_merge,
            'required_contexts' => $required_contexts,
            'payload' => $payload,
            'environment' => $environment,
            'description' => $description,
            'transient_environment' => $transient_environment,
            'production_environment' => $production_environment,
        ];

        $array = array_filter($array);

        $url = implode('/', $this->urlCommon);

        HTTP::post($url, json_encode($array));
    }

    public function update()
    {

    }

    /**
     * @param string $id
     */
    public function getStatus(string $id)
    {
        $urlCommon = $this->urlCommon;

        array_push($urlCommon, $id, 'statuses');

        $url = implode('/', $urlCommon);

        HTTP::get($url);
    }

    /**
     * @param string $id
     * @param string $statusId
     * @return mixed
     */
    public function getSingleStatus(string $id, string $statusId)
    {
        $urlCommon = $this->urlCommon;

        array_push($urlCommon, $id, 'statuses', $statusId);

        $url = implode('/', $urlCommon);

        return HTTP::get($url);
    }

    /**
     * @param string $id
     * @param string $state
     * @param string|null $target_url
     * @param string|null $log_url
     * @param string|null $description
     * @param string|null $environment_url
     * @param bool $auto_inactive
     */
    public function createStatus(string $id,
                                 string $state,
                                 string $target_url = null,
                                 string $log_url = null,
                                 string $description = null,
                                 string $environment_url = null,
                                 bool $auto_inactive = true)
    {

    }

}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Deployment;

use Exception;
use KhsCI\Service\CICommon;

/**
 * 展示 CI 状态
 *
 * @see https://developer.github.com/v3/repos/deployments/
 */
class GitHubClient
{
    use CICommon;

    /**
     * List deployments.
     *
     * @param string $repo_full_name
     * @param string $sha            The SHA that was recorded at creation time. Default: <code>none<code>
     * @param string $ref            The name of the ref. This can be a branch, tag, or SHA. Default: <code>none<code>
     * @param string $task           The name of the task for the deployment (e.g., <code>deploy<code> or
     *                               <code>deploy:migrations<code>). Default: <code>none<code>
     * @param string $environment    The name of the environment that was deployed to (e.g., <code>staging<code> or
     *                               <code>production<code>). Default: <code>none<code>
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name, string $sha, string $ref, string $task, string $environment)
    {
        $queryParameters = http_build_query([
            'sha' => $sha,
            'ref' => $ref,
            'task' => $task,
            'environment' => $environment,
        ]);

        $url = implode('/', [
                $this->api_url, 'repos', $repo_full_name, 'deployments',
            ]
        );

        return $this->curl->get($url.'?'.$queryParameters, null);
    }

    /**
     * Get a single deployment.
     *
     * @param string $repo_full_name
     * @param string $id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getSingleInfo(string $repo_full_name, string $id)
    {
        $url = implode('/', [
                $this->api_url, 'repos', $repo_full_name, 'deployments', $id,
            ]
        );

        return $this->curl->get($url);
    }

    /**
     * Create a deployment.
     *
     * @param string      $repo_full_name
     * @param string      $ref
     * @param string      $task
     * @param bool        $auto_merge
     * @param array|null  $required_contexts
     * @param string|null $payload
     * @param string      $environment
     * @param string|null $description
     * @param bool|null   $transient_environment
     * @param bool|null   $production_environment
     *
     * @throws Exception
     */
    public function create(string $repo_full_name,
                           string $ref,
                           string $task = 'deploy',
                           bool $auto_merge = true,
                           array $required_contexts = null,
                           string $payload = null,
                           string $environment = 'production',
                           string $description = null,
                           bool $transient_environment = null,
                           bool $production_environment = null): void
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

        $url = implode('/', [
                $this->api_url, 'repos', $repo_full_name, 'deployments',
            ]
        );

        $this->curl->post($url, json_encode($array));
    }

    /**
     * List deployment statuses.
     *
     * @param string $repo_full_name
     * @param string $id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getStatus(string $repo_full_name, string $id)
    {
        $url = implode('/', [
                $this->api_url, 'repos', $repo_full_name, 'deployments', $id, 'statuses',
            ]
        );

        return $this->curl->get($url);
    }

    /**
     * Get a single deployment status.
     *
     * @param string $repo_full_name
     * @param string $id
     * @param string $statusId
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getSingleStatus(string $repo_full_name, string $id, string $statusId)
    {
        $url = implode('/', [
                $this->api_url, 'repos', $repo_full_name, 'deployments', $id, 'statuses', $statusId,
            ]
        );

        return $this->curl->get($url);
    }

    /**
     * Create a deployment status.
     *
     * 201
     *
     * @param string      $repo_full_name  repo full name
     * @param string      $id
     * @param string      $state           error, failure, inactive, pending, or success
     * @param string|null $log_url
     * @param string|null $description
     * @param string|null $environment_url
     * @param bool        $auto_inactive
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function createStatus(string $repo_full_name,
                                 string $id,
                                 string $state,
                                 string $log_url = null,
                                 string $description = null,
                                 string $environment_url = null,
                                 bool $auto_inactive = true)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/deployments/'.$id.'/statuses';

        $data = [
            'state' => $state,
            'log_url' => $log_url,
            'description' => $description,
            'environment_url' => $environment_url,
            'auto_inactive' => $auto_inactive,
        ];

        return $this->curl->post($url, json_encode(array_filter($data)), ['Accept' => 'application/vnd.github.machine-man-preview.speedy-preview.ant-man-preview+json']);
    }
}

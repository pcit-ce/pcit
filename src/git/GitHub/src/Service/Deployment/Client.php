<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Deployment;

use PCIT\GPI\ServiceClientCommon;

/**
 * 展示 CI 状态
 *
 * @see https://developer.github.com/v3/repos/deployments/
 */
class Client
{
    use ServiceClientCommon;

    /**
     * List deployments.
     *
     * @param string $sha         The SHA that was recorded at creation time. Default: <code>none<code>
     * @param string $ref         The name of the ref. This can be a branch, tag, or SHA. Default: <code>none<code>
     * @param string $task        The name of the task for the deployment (e.g., <code>deploy<code> or
     *                            <code>deploy:migrations<code>). Default: <code>none<code>
     * @param string $environment The name of the environment that was deployed to (e.g., <code>staging<code> or
     *                            <code>production<code>). Default: <code>none<code>
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function list(string $repo_full_name, string $sha, string $ref, string $task, string $environment)
    {
        $queryParameters = http_build_query(compact('sha', 'ref', 'task', 'environment'));

        $url = implode(
            '/',
            [
                $this->api_url, 'repos', $repo_full_name, 'deployments',
            ]
        );

        return $this->curl->get($url.'?'.$queryParameters, null);
    }

    /**
     * Get a single deployment.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getSingleInfo(string $repo_full_name, string $id)
    {
        $url = implode(
            '/',
            [
                $this->api_url, 'repos', $repo_full_name, 'deployments', $id,
            ]
        );

        return $this->curl->get($url);
    }

    /**
     * Create a deployment.
     *
     * @throws \Exception
     */
    public function create(
        string $repo_full_name,
        string $ref,
        string $task = 'deploy',
        bool $auto_merge = true,
        array $required_contexts = null,
        string $payload = null,
        string $environment = 'production',
        string $description = null,
        bool $transient_environment = null,
        bool $production_environment = null
    ): void
    {
        $result = compact('ref', 'task', 'auto_merge', 'required_contexts', 'payload', 'environment', 'description', 'transient_environment', 'production_environment');

        $result = array_filter($result);

        $url = implode(
            '/',
            [
                $this->api_url, 'repos', $repo_full_name, 'deployments',
            ]
        );

        $this->curl->post($url, json_encode($result));
    }

    /**
     * List deployment statuses.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getStatus(string $repo_full_name, string $id)
    {
        $url = implode(
            '/',
            [
                $this->api_url, 'repos', $repo_full_name, 'deployments', $id, 'statuses',
            ]
        );

        return $this->curl->get($url);
    }

    /**
     * Get a single deployment status.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getSingleStatus(string $repo_full_name, string $id, string $statusId)
    {
        $url = implode(
            '/',
            [
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
     * @param string $repo_full_name repo full name
     * @param string $state          error, failure,inactive,in_progress,queued,pending, or success
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function createStatus(
        string $repo_full_name,
        string $id,
        string $state,
        string $log_url = null,
        string $description = null,
        string $environment = 'production',
        string $environment_url = null,
        bool $auto_inactive = true
    )
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/deployments/'.$id.'/statuses';

        $data = array_filter(
            compact('state', 'log_url', 'description', 'environment', 'environment_url', 'auto_inactive')
        );

        return $this->curl->post(
            $url,
            json_encode(array_filter($data)),
            ['Accept' => 'application/vnd.github.flash-preview+json,application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.ant-man-preview+json']
        );
    }
}

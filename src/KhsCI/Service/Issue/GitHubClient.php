<?php

declare(strict_types=1);

namespace KhsCI\Service\Issue;

use Curl\Curl;
use Exception;
use KhsCI\Support\Log;
use TencentAI\TencentAI;

class GitHubClient
{
    /**
     * @var Curl
     */
    private $curl;

    private $api_url;

    private $header = [
        'Accept' => 'application/vnd.github.symmetra-preview+json',
    ];

    /**
     * @var TencentAI
     */
    private $tencent_ai;

    public function __construct(Curl $curl, string $api_url, TencentAI $tencent_ai)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;

        $this->tencent_ai = $tencent_ai;
    }

    /**
     * List all issues assigned to the authenticated user across all visible repositories including owned repositories,
     * member repositories, and organization repositories:.
     */
    public function list(): void
    {
        $url = $this->api_url.'/issues';
    }

    /**
     * List issues for a repository.
     *
     * @param string $repo_full_name
     */
    public function listRepositoryIssues(string $repo_full_name): void
    {
    }

    /**
     * Get a single issue.
     *
     * 201
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getSingle(string $repo_full_name, int $issue_number)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number;

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * @param string $repo_full_name
     * @param string $title
     * @param string $body
     * @param int    $milestone
     * @param array  $labels
     * @param array  $assignees
     *
     * @throws Exception
     */
    public function create(string $repo_full_name,
                           string $title,
                           string $body,
                           int $milestone,
                           array $labels,
                           array $assignees): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues';

        $data = [
            'title' => $title,
            'body' => $body,
            'milestone' => $milestone,
            'labels' => $labels,
            'assignees' => $assignees,
        ];

        $this->curl->post($url, json_encode(array_filter($data)), $this->header);

        $http_return_code = $this->curl->getCode();

        if (201 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code Is Not 201 '.$http_return_code);

            throw new Exception('Create Issue Error', $http_return_code);
        }
    }

    /**
     * @param string $repo_full_name
     * @param int    $issue_number
     * @param string $title
     * @param string $body
     * @param string $state          State of the issue. Either open or closed.
     * @param int    $milestone
     * @param array  $labels
     * @param array  $assignees
     *
     * @throws Exception
     */
    public function edit(string $repo_full_name,
                         int $issue_number,
                         string $title,
                         string $body,
                         string $state,
                         int $milestone,
                         array $labels,
                         array $assignees): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number;

        $data = [
            'title' => $title,
            'body' => $body,
            'state' => $state,
            'milestone' => $milestone,
            'labels' => $labels,
            'assignees' => $assignees,
        ];

        $this->curl->patch($url, json_encode(array_filter($data)), $this->header);

        $http_return_code = $this->curl->getCode();

        if (200 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code Is Not 200 '.$http_return_code);

            throw new Exception('Edit Issue Error '.$http_return_code);
        }
    }

    /**
     * 204.
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     * @param string $lock_reason    The reason for locking the issue or pull request conversation. Lock will fail if
     *                               you don't use one of these reasons: off-topic too heated resolved spam
     *
     * @throws Exception
     */
    public function lock(string $repo_full_name, int $issue_number, string $lock_reason = null): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/lock';

        if ($lock_reason) {
            $data = [
                'locked' => true,
                'active_lock_reason' => $lock_reason,
            ];
            $this->curl->put($url, json_encode($data), ['Accept' => 'application/vnd.github.sailor-v-preview+json']);
        } else {
            $this->curl->put($url, null, ['Accept' => 'application/vnd.github.sailor-v-preview+json']);
        }

        $http_return_code = $this->curl->getCode();

        if (204 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code Is Not 204 '.$http_return_code);

            throw new Exception('Lock Issue Error', $http_return_code);
        }
    }

    /**
     * @param string $repo_full_name
     * @param int    $issue_number
     *
     * @throws Exception
     */
    public function unlock(string $repo_full_name, int $issue_number): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/lock';

        $this->curl->delete($url);

        $http_return_code = $this->curl->getCode();

        if (204 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code Is Not 204 '.$http_return_code);

            throw new Exception('Unlock Issue Error', $http_return_code);
        }
    }
}

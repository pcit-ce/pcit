<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use Curl\Curl;
use Exception;

/**
 * The status API allows external services to mark commits with an
 * error, failure, pending, or success state, which is then reflected in pull requests involving those commits.
 *
 * @see https://developer.github.com/v3/repos/statuses/
 */
class StatusClient
{
    private $curl;

    private $api_url;

    /**
     * Status constructor.
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $username, string $repo, string $ref)
    {
        $url = implode('/', [
                $this->api_url, 'repos', $username, $repo, 'commits', $ref, 'statuses',
            ]
        );

        return $this->curl->get($url);
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function create(string $username,
                           string $repo,
                           string $commit_sha,
                           string $state = 'pending',
                           string $target_url = 'https://ci.khs1994.com',
                           string $context = 'continuous-integration/ci.khs1994.com/push',
                           string $description = 'The analysis or builds is pending'
    ) {
        $url = implode('/', [
                $this->api_url, 'repos', $username, $repo, 'statuses', $commit_sha,
            ]
        );

        $data = json_encode([
            'state' => $state,
            'target_url' => $target_url,
            'description' => $description,
            'context' => $context,
        ]);

        $output = $this->curl->post($url, $data);

        $http_return_code = $this->curl->getCode();

        if (201 !== $http_return_code) {
            \Log::debug('Http Return code is not 201 '.$http_return_code);
        }

        return $output;
    }

    /**
     * 获取某分支的组合状态信息.
     *
     * @param $username
     * @param $repo
     * @param $ref
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listCombinedStatus($username, $repo, $ref)
    {
        $url = implode('/', [
                $this->api_url, 'repos', $username, $repo, 'commits', $ref, 'status',
            ]
        );

        return json_decode($this->curl->get($url), true);
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Repositories;

use Curl\Curl;
use Exception;

/**
 * The status API allows external services to mark commits with an
 * error, failure, pending, or success state, which is then reflected in pull requests involving those commits.
 *
 * @see https://developer.github.com/v3/repos/statuses/
 */
class Status
{
    const API_URL = 'https://api.github.com';

    private static $curl;

    /**
     * Status constructor.
     *
     * @param Curl $curl
     */
    public function __construct(Curl $curl)
    {
        self::$curl = $curl;
    }

    /**
     * @param string $username
     * @param string $repo
     * @param string $ref
     *
     * @return mixed
     * @throws Exception
     */
    public function list(string $username, string $repo, string $ref)
    {
        $url = [self::API_URL, 'repos', $username, $repo, 'commits', $ref, 'statuses'];

        $url = implode('/', $url);

        return self::$curl->get($url);
    }

    /**
     * @param string $username
     * @param string $repo
     * @param string $commit_sha
     * @param string $state
     * @param string $target_url
     * @param string $description
     * @param string $context
     * @param string $access_token
     *
     * @return mixed
     * @throws Exception
     */
    public function create(string $username,
                           string $repo,
                           string $commit_sha,
                           string $state = 'pending',
                           string $target_url = 'https://ci.khs1994.com',
                           string $description = 'The analysis or builds is pending',
                           string $context = 'continuous-integration/khsci/push',
                           string $access_token = null
    )
    {
        $url = [self::API_URL, 'repos', $username, $repo, 'statuses', $commit_sha];

        $url = implode('/', $url);

        $data = json_encode([
            'state' => $state,
            'target_url' => $target_url,
            'description' => $description,
            'context' => $context,
        ]);

        if ($access_token) {
            return self::$curl->post($url, $data, ['Authorization' => 'token '.$access_token]);
        }

        return self::$curl->post($url, $data);
    }

    /**
     * 获取某分支的组合状态信息.
     *
     * @param $username
     * @param $repo
     * @param $ref
     *
     * @return mixed
     * @throws Exception
     */
    public function listCombinedStatus($username, $repo, $ref)
    {
        $url = [self::API_URL, 'repos', $username, $repo, 'commits', $ref, 'status'];

        $url = implode('/', $url);

        return json_decode(self::$curl->get($url), true);
    }
}

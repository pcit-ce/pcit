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
    private static $curl;

    private static $api_url;

    /**
     * Status constructor.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        self::$curl = $curl;

        self::$api_url = $api_url;
    }

    /**
     * @param string $username
     * @param string $repo
     * @param string $ref
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $username, string $repo, string $ref)
    {
        $url = implode('/', [
                self::$api_url, 'repos', $username, $repo, 'commits', $ref, 'statuses',
            ]
        );

        return self::$curl->get($url);
    }

    /**
     * @param string $username
     * @param string $repo
     * @param string $commit_sha
     * @param string $state
     * @param string $target_url
     * @param string $context
     * @param string $description
     *
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
                self::$api_url, 'repos', $username, $repo, 'statuses', $commit_sha,
            ]
        );

        $data = json_encode([
            'state' => $state,
            'target_url' => $target_url,
            'description' => $description,
            'context' => $context,
        ]);

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
     *
     * @throws Exception
     */
    public function listCombinedStatus($username, $repo, $ref)
    {
        $url = implode('/', [
                self::$api_url, 'repos', $username, $repo, 'commits', $ref, 'status',
            ]
        );

        return json_decode(self::$curl->get($url), true);
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Status;

use KhsCI\Support\HTTP;

/**
 * The status API allows external services to mark commits with an
 * error, failure, pending, or success state, which is then reflected in pull requests involving those commits.
 *
 * @see https://developer.github.com/v3/repos/statuses/
 */
class GitHub
{
    const API_URL = 'https://api.github.com';

    /**
     * @param $username
     * @param $repo
     * @param $ref
     *
     * @return mixed
     */
    public function list($username, $repo, $ref)
    {
        $url = [self::API_URL, 'repos', $username, $repo, 'commits', $ref, 'statuses'];

        $url = implode('/', $url);

        return HTTP::get($url);
    }

    /**
     * @param string $username
     * @param string $repo
     * @param string $commit_sha
     * @param string $accessToken
     * @param string $state
     * @param string $target_url
     * @param string $description
     * @param string $context
     *
     * @return mixed
     */
    public function create(string $username,
                           string $repo,
                           string $commit_sha,
                           string $accessToken,
                           string $state = 'pending',
                           string $target_url = 'https://ci.khs1994.com',
                           string $description = 'The analysis or builds is pending',
                           string $context = 'continuous-integration/khsci/push')
    {
        $url = [self::API_URL, 'repos', $username, $repo, 'statuses', $commit_sha];

        $url = implode('/', $url);

        $data = json_encode([
            'state' => $state,
            'target_url' => $target_url,
            'description' => $description,
            'context' => $context,
        ]);

        var_dump($url);

        return HTTP::post($url, $data, ['Authorization' => 'token '.$accessToken]);
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Issue;

use Curl\Curl;
use Exception;
use KhsCI\Support\Log;

/**
 * Class Assignees.
 *
 * @see  https://developer.github.com/v3/issues/assignees/
 */
class Assignees
{
    private static $curl;

    private static $api_url;

    private static $header = [
        'Accept' => 'application/vnd.github.symmetra-preview+json',
    ];

    /**
     * Assignees constructor.
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
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name)
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/assignees';

        return self::$curl->get($url);
    }

    /**
     * 204 404.
     *
     * @param string $repo_full_name
     * @param string $assignees
     *
     * @return bool
     *
     * @throws Exception
     */
    public function check(string $repo_full_name, string $assignees)
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/assignees/'.$assignees;

        self::$curl->get($url);

        if (204 !== self::$curl->getCode()) {
            return false;
        }

        return true;
    }

    /**
     * Add assignees to an issue.
     *
     * 201
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     * @param array  $assignees
     *
     * @throws Exception
     */
    public function add(string $repo_full_name, int $issue_number, array $assignees): void
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/assignees';

        $data = [
            'assignees' => $assignees,
        ];

        self::$curl->post($url, json_encode($data), self::$header);

        $http_return_code = self::$curl->getCode();

        if (201 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return code is not 201 '.$http_return_code);

            throw new Exception('Add Assignees in Issue Error', $http_return_code);
        }
    }

    /**
     * Remove assignees from an issue.
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     * @param array  $assignees
     *
     * @throws Exception
     */
    public function remove(string $repo_full_name, int $issue_number, array $assignees): void
    {
        $url = self::$api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/assignees';

        $data = [
            'assignees' => $assignees,
        ];

        self::$curl->delete($url, json_encode($data), self::$header);

        $http_return_code = self::$curl->getCode();

        if (200 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code is not 200 '.$http_return_code);

            throw new Exception('Remove Assignees from Issue Error', $http_return_code);
        }
    }
}

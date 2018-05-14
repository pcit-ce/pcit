<?php

declare(strict_types=1);

namespace KhsCI\Service\Checks;

use Curl\Curl;
use Exception;

class Suites
{
    /**
     * @var Curl
     */
    private static $curl;

    private static $api_url;

    private static $header = [
        'Accept' => 'application/vnd.github.antiope-preview+json'
    ];

    /**
     * Suites constructor.
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
     * Get a single check suite.
     *
     * @param string $repo_full_name
     * @param int    $check_suite_id
     *
     * @return mixed
     * @throws Exception
     */
    public function getSingle(string $repo_full_name, int $check_suite_id)
    {
        $url = static::$api_url.'/repos/'.$repo_full_name.'/check-suites/'.$check_suite_id;

        return static::$curl->get($url, null, static::$header);
    }

    /**
     * List check suites for a specific ref.
     *
     * @param string $repo_full_name
     * @param string $ref Required. The ref can be a SHA, branch name, or a tag name.
     * @param int    $app_id
     * @param string $check_name
     *
     * @return mixed
     * @throws Exception
     */
    public function listSpecificRef(string $repo_full_name,
                                    string $ref,
                                    int $app_id = null,
                                    string $check_name = null)
    {
        $url = static::$api_url.'/repos/'.$repo_full_name.'/commits/'.$ref.'/check-suites';

        $data = [
            'app_id' => $app_id,
            'check_name' => $check_name,
        ];

        $url = $url.'?'.http_build_query($data);

        return static::$curl->get($url, null, self::$header);
    }

    /**
     * Set preferences for check suites on a repository.
     */
    public function setPreferences(): void
    {
    }

    /**
     * By default, check suites are automatically created when you create a check run.
     */
    public function create(): void
    {
    }

    public function request(): void
    {
    }
}

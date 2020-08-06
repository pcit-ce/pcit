<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Checks;

use Curl\Curl;

class Suites
{
    /**
     * @var Curl
     */
    private $curl;

    private $api_url;

    private $header = [
        'Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json,application/vnd.github.antiope-preview+json',
    ];

    /**
     * Suites constructor.
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * Get a single check suite.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getSingle(string $repo_full_name, int $check_suite_id)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-suites/'.$check_suite_id;

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * List check suites for a specific ref.
     *
     * @param string $ref        Required. The ref can be a SHA, branch name, or a tag name.
     * @param int    $app_id
     * @param string $check_name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function listSpecificRef(
        string $repo_full_name,
        string $ref,
        int $app_id = null,
        string $check_name = null
    )
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/commits/'.$ref.'/check-suites';

        $data = [
            'app_id' => $app_id,
            'check_name' => $check_name,
        ];

        $url = $url.'?'.http_build_query($data);

        return $this->curl->get($url, null, $this->header);
    }

    /**
     * Set preferences for check suites on a repository.
     *
     * @throws \Exception
     *
     * @return mixed
     *
     * @example
     * <pre>
     * [
     *     'app_id' => 4,
     *     'setting' => false
     * ],[
     *     'app_id' => 4,
     *     'setting' => false
     * ]
     * </pre>
     */
    public function setPreferences(string $repo_full_name, array $auto_trigger_checks)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-suites/preferences';

        $data = [
            'auto_trigger_checks' => [
                $auto_trigger_checks,
            ],
        ];

        return $this->curl->patch($url, json_encode($data), $this->header);
    }

    /**
     * By default, check suites are automatically created when you create a check run.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(string $repo_full_name, string $head_branch, string $head_sha)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-suites';

        $data = [
            'head_branch' => $head_branch,
            'head_sha' => $head_sha,
        ];

        return $this->curl->post($url, json_encode($data), $this->header);
    }

    /**
     * Rerequest check suite.
     *
     * Triggers GitHub to create a new check suite, without pushing new code to a repository.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function request(string $repo_full_name, int $check_suite_id)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/check-suites/'.$check_suite_id.'/rerequest';

        return $this->curl->post($url, null, $this->header);
    }
}

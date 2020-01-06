<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Issue;

use Curl\Curl;
use Exception;

/**
 * Class Assignees.
 *
 * @see  https://developer.github.com/v3/issues/assignees/
 */
class AssigneesClient
{
    private $curl;

    private $api_url;

    private $header = [
        'Accept' => 'application/vnd.github.machine-man-preview+json;
        application/vnd.github.speedy-preview+json;
        application/vnd.github.symmetra-preview+json',
    ];

    /**
     * Assignees constructor.
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * List assignees.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/assignees';

        return $this->curl->get($url);
    }

    /**
     * Check assignee.
     *
     * 204 404.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function check(string $repo_full_name, string $assignees)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/assignees/'.$assignees;

        $this->curl->get($url);

        if (204 !== $this->curl->getCode()) {
            return false;
        }

        return true;
    }

    /**
     * Add assignees to an issue.
     *
     * 201
     *
     * @throws Exception
     */
    public function add(string $repo_full_name, int $issue_number, array $assignees): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/assignees';

        $this->curl->post($url, json_encode(compact('data')), $this->header);

        $http_return_code = $this->curl->getCode();

        if (201 !== $http_return_code) {
            \Log::debug('Http Return code is not 201 '.$http_return_code);

            throw new Exception('Add Assignees in Issue Error', $http_return_code);
        }
    }

    /**
     * Remove assignees from an issue.
     *
     * @throws Exception
     */
    public function remove(string $repo_full_name, int $issue_number, array $assignees): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/assignees';

        $this->curl->delete($url, json_encode(compact('assignees')), $this->header);

        $http_return_code = $this->curl->getCode();

        if (200 !== $http_return_code) {
            \Log::debug('Http Return Code is not 200 '.$http_return_code);

            throw new Exception('Remove Assignees from Issue Error', $http_return_code);
        }
    }
}

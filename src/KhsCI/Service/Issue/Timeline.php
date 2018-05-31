<?php

declare(strict_types=1);

namespace KhsCI\Service\Issue;

use Curl\Curl;
use Exception;

/**
 * Class Timeline.
 *
 * @see https://developer.github.com/v3/issues/timeline/
 */
class Timeline
{
    private $curl;

    private $api_url;

    /**
     * List events for an issue.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;
        $this->api_url = $api_url;
    }

    /**
     * @param string $repo_full_name
     * @param int    $issue_number
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name, int $issue_number)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/timeline';

        return $this->curl->get($url);
    }
}

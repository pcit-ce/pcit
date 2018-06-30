<?php

declare(strict_types=1);

namespace KhsCI\Service\Issue;

use KhsCI\Service\CICommon;

/**
 * Class Events.
 *
 * @see https://developer.github.com/v3/issues/events/
 */
class EventsGitHubClient
{
    use CICommon;

    /**
     * List events for an issue.
     *
     * @param string $repo_full_name
     * @param int    $issue_number
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listForIssue(string $repo_full_name, int $issue_number)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/events');
    }

    /**
     * List events for a repository.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listForRepository(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/events');
    }

    /**
     * Get a single event.
     *
     * @param string $repo_full_name
     * @param int    $event_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getSingleEvent(string $repo_full_name, int $event_id)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/events/'.$event_id);
    }
}

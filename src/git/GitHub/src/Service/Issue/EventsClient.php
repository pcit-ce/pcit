<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Issue;

use PCIT\GPI\ServiceClientCommon;

/**
 * Class Events.
 *
 * @see https://developer.github.com/v3/issues/events/
 */
class EventsClient
{
    use ServiceClientCommon;

    /**
     * List events for an issue.
     *
     * @return mixed
     */
    public function listForIssue(string $repo_full_name, int $issue_number)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/'.$issue_number.'/events');
    }

    /**
     * List events for a repository.
     *
     * @return mixed
     */
    public function listForRepository(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/events');
    }

    /**
     * Get a single event.
     *
     * @return mixed
     */
    public function getSingleEvent(string $repo_full_name, int $event_id)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/events/'.$event_id);
    }
}

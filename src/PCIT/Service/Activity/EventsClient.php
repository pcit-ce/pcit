<?php

declare(strict_types=1);

namespace PCIT\Service\GitHub\Activity;

use PCIT\Service\CICommon;

class EventsClient
{
    use CICommon;

    /**
     * List public events.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function public()
    {
        return $this->curl->get($this->api_url.'/events');
    }

    /**
     * List repository events.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function repository(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/events');
    }

    /**
     * List issue events for a repository.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function issue(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/issues/events');
    }

    /**
     * List public events for a network of repositories.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function network(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/networks/'.$repo_full_name.'/events');
    }

    /**
     * List public events for an organization.
     *
     * @param $org_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function publicOrganization($org_name)
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/events');
    }

    /**
     * List events for an organization.
     *
     * @param string $username
     * @param string $org_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function organization(string $username, string $org_name)
    {
        return $this->curl->get($this->api_url.'/users/'.$username.'/events/org/'.$org_name);
    }

    /**
     * List events that a user has received.
     *
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function userReceived(string $username)
    {
        return $this->curl->get($this->api_url.'/users/'.$username.'/received_events');
    }

    /**
     * List public events that a user has received.
     *
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function publicUserReceived(string $username)
    {
        return $this->curl->get($this->api_url.'/users/'.$username.'/received_events/public');
    }

    /**
     * List events performed by a user.
     *
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function performedByUser(string $username)
    {
        return $this->curl->get($this->api_url.'/users/'.$username.'/events');
    }

    /**
     * List public events performed by a user.
     *
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function publicPerformedByUser(string $username)
    {
        return $this->curl->get($this->api_url.'/users/'.$username.'/events/public');
    }
}

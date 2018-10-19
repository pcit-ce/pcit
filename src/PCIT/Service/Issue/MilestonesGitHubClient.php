<?php

declare(strict_types=1);

namespace PCIT\Service\Issue;

use PCIT\Service\CICommon;

class MilestonesGitHubClient
{
    use CICommon;

    /**
     * List milestones for a repository.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function list(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/milestones');
    }

    /**
     * Get a single milestone.
     *
     * @param string $repo_full_name
     * @param string $milestone_number
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function get(string $repo_full_name, string $milestone_number)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/milestones/'.$milestone_number);
    }

    /**
     * Create a milestone.
     *
     * 201
     *
     * @param string $repo_full_name repo full name
     * @param string $title
     * @param string $state          open or closed
     * @param string $description
     * @param string $due_on
     *
     * @throws \Exception
     */
    public function create(string $repo_full_name, string $title, ?string $description, ?string $due_on, string $state = 'open'): void
    {
        $data = [
            'title' => $title,
            'state' => $state,
            'description' => $description,
            'due_on' => $due_on,
        ];

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/milestones', json_encode(array_filter($data)));
    }

    /**
     * Update a milestone.
     *
     * @param string      $repo_full_name
     * @param int         $milestone_number
     * @param string      $title
     * @param null|string $description
     * @param null|string $due_on
     * @param string      $state
     *
     * @throws \Exception
     */
    public function update(string $repo_full_name, int $milestone_number, string $title, ?string $description, ?string $due_on, string $state = 'open'): void
    {
        $data = [
            'title' => $title,
            'state' => $state,
            'description' => $description,
            'due_on' => $due_on,
        ];

        $this->curl->patch($this->api_url.'/repos/'.$repo_full_name.'/milestones/'.$milestone_number, json_encode(array_filter($data)));
    }

    /**
     * Delete a milestone.
     *
     * 204
     *
     * @param string $repo_full_name
     * @param int    $milestone_number
     *
     * @throws \Exception
     */
    public function delete(string $repo_full_name, int $milestone_number): void
    {
        $this->curl->delete($this->api_url.'/repos/'.$repo_full_name.'/milestones/'.$milestone_number);
    }
}

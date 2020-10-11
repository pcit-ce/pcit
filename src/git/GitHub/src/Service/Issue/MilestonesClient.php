<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Issue;

use PCIT\GPI\ServiceClientCommon;

class MilestonesClient
{
    use ServiceClientCommon;

    /**
     * List milestones for a repository.
     *
     * @return mixed
     */
    public function list(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/milestones');
    }

    /**
     * Get a single milestone.
     *
     * @return mixed
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
     * @param string $state          open or closed
     * @param string $description
     * @param string $due_on
     */
    public function create(string $repo_full_name, string $title, ?string $description, ?string $due_on, string $state = 'open'): void
    {
        $this->curl->post(
            $this->api_url.'/repos/'.$repo_full_name.'/milestones',
            json_encode(array_filter(compact(
                'title',
                'state',
                'description',
                'due_on'
            )))
        );
    }

    /**
     * Update a milestone.
     */
    public function update(string $repo_full_name, int $milestone_number, string $title, ?string $description, ?string $due_on, string $state = 'open'): void
    {
        $this->curl->patch(
            $this->api_url.'/repos/'.$repo_full_name.'/milestones/'.$milestone_number,
            json_encode(array_filter(compact(
                'title',
                'state',
                'description',
                'due_on'
            )))
        );
    }

    /**
     * Delete a milestone.
     *
     * 204
     */
    public function delete(string $repo_full_name, int $milestone_number): void
    {
        $this->curl->delete($this->api_url.'/repos/'.$repo_full_name.'/milestones/'.$milestone_number);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Service\Activity;

use Exception;
use PCIT\Service\CICommon;

class WatchingGitHubClient
{
    use CICommon;

    /**
     * List watchers.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/subscribers');
    }

    /**
     * List repositories being watched.
     *
     * @param string|null $username
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listRepositoryBeingWatched(string $username = null)
    {
        if ($username) {
            return $this->curl->get($this->api_url.'/users/'.$username.'/subscriptions');
        }

        return $this->curl->get($this->api_url.'/user/subscriptions');
    }

    /**
     * Get a Repository Subscription.
     *
     * 检查是否 watching repo
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getRepositorySubscription(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/subscription');
    }

    /**
     * Set a Repository Subscription.
     *
     * @param string $repo_full_name
     * @param bool   $subscribed
     * @param bool   $ignored
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function setRepositorySubscription(string $repo_full_name, bool $subscribed, bool $ignored)
    {
        return $this->curl->put($this->api_url.'/repos/'.$repo_full_name.'/subscription?'.http_build_query([
                    'subscribed' => $subscribed,
                    'ignored' => $ignored,
                ]
            )
        );
    }

    /**
     * Delete a Repository Subscription.
     *
     * 204
     *
     * @param string $repo_full_name
     *
     * @throws Exception
     */
    public function deleteRepositorySubscription(string $repo_full_name): void
    {
        $this->curl->delete($this->api_url.'/repos/'.$repo_full_name.'/subscription');
    }
}

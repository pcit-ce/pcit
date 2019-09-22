<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Activity;

use PCIT\GitHub\Service\CICommon;

class NotificationsClient
{
    use CICommon;

    /**
     * List your notifications.
     *
     * @param bool   $all
     * @param bool   $participating
     * @param string $since
     * @param string $before
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function list(bool $all = false, bool $participating, string $since, string $before)
    {
        $data = [
            'all' => $all,
            'participating' => $participating,
            'since' => $since,
            'before' => $before,
        ];

        return $this->curl->get($this->api_url.'/notifications?'.http_build_query($data));
    }

    /**
     * List your notifications in a repository.
     *
     * @param string $repo_full_name
     * @param bool   $all
     * @param bool   $participating
     * @param string $since
     * @param string $before
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function repository(string $repo_full_name, bool $all = false, bool $participating, string $since, string $before)
    {
        $data = [
            'all' => $all,
            'participating' => $participating,
            'since' => $since,
            'before' => $before,
        ];

        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/notifications?'.http_build_query($data));
    }

    /**
     * Mark as read.
     *
     * 205
     *
     * @param string $last_read_at ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function markAsRead(string $last_read_at)
    {
        return $this->curl->put($this->api_url.'/notifications?'.http_build_query(['last_read_at' => $last_read_at]));
    }

    /**
     * Mark notifications as read in a repository.
     *
     * @param string $repo_full_name
     * @param string $last_read_at   ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function markAsReadInRepository(string $repo_full_name, string $last_read_at)
    {
        return $this->curl->put($this->api_url.'/repos/'.$repo_full_name.'/notifications?'.http_build_query(['last_read_at' => $last_read_at]));
    }

    /**
     * View a single thread.
     *
     * @param int $thread_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function viewSingleThread(int $thread_id)
    {
        return $this->curl->get($this->api_url.'/notifications/threads/'.$thread_id);
    }

    /**
     * Mark a thread as read.
     *
     * 205
     *
     * @param int $thread_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function markThreadAsRead(int $thread_id)
    {
        return $this->curl->patch($this->api_url.'/notifications/threads/'.$thread_id);
    }

    /**
     * Get a thread subscription.
     *
     * @param int $thread_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getThreadSubscription(int $thread_id)
    {
        return $this->curl->get($this->api_url.'/notifications/threads/'.$thread_id.'/subscription');
    }

    /**
     * Set a thread subscription.
     *
     * @param int  $thread_id
     * @param bool $ignored
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function setThreadSubscription(int $thread_id, bool $ignored = false)
    {
        return $this->curl->put($this->api_url.'/notifications/threads/'.$thread_id.'/subscription?'.http_build_query(['ignored' => $ignored]));
    }

    /**
     * Delete a thread subscription.
     *
     * 204
     *
     * @param int $thread_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function deleteThreadSubscription(int $thread_id)
    {
        return $this->curl->delete($this->api_url.'/notifications/threads/'.$thread_id.'/subscription');
    }
}

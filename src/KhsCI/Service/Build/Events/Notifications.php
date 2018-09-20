<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Events;

class Notifications
{
    public $build_key_id;

    public $notifications;

    /**
     * Notification constructor.
     *
     * @param int   $build_key_id
     * @param mixed $notifications
     */
    public function __construct(int $build_key_id, $notifications = null)
    {
        $this->build_key_id = $build_key_id;
        $this->notifications = $notifications;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        if (!$this->notifications) {
            return;
        }

        if (!$email = $this->notifications->email ?? null) {
            return;
        }

        \KhsCI\Support\Cache::store()
            ->hSet(
                'notifications',
                (string) $this->build_key_id,
                json_encode($email)
            );
    }
}

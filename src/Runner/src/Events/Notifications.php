<?php

declare(strict_types=1);

namespace PCIT\Builder\Events;

use PCIT\Support\CacheKey;

class Notifications
{
    public $build_key_id;

    public $notifications;

    /**
     * Notification constructor.
     *
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

        \PCIT\Framework\Support\Cache::store()
            ->hSet(
                CacheKey::notificationsHashKey($this->build_key_id),
                'email',
                json_encode($email)
            );
    }
}

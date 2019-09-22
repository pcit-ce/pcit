<?php

declare(strict_types=1);

namespace App\Events;

/**
 * 通知.
 */
class Notification
{
    public function __construct(int $build_key_id, \Throwable $exception)
    {
    }

    public function register(): void
    {
    }
}

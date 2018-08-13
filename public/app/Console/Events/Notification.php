<?php

declare(strict_types=1);

namespace App\Console\Events;

class Notification
{
    public function __construct(int $build_key_id, \Throwable $exception)
    {
    }

    public function register(): void
    {
    }
}

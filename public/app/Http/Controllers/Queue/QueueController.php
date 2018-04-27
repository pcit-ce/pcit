<?php

declare(strict_types=1);

namespace App\Http\Controllers\Queue;

use KhsCI\Service\Queue\Queue;

class QueueController
{
    public function __invoke(): void
    {
        $queue = new Queue();
        $queue();
    }
}

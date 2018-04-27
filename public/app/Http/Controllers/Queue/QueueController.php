<?php

namespace App\Http\Controllers\Queue;

use KhsCI\Service\Queue\Queue;

class QueueController
{
    public function __invoke()
    {
        $queue = new Queue();
        $queue();
    }
}

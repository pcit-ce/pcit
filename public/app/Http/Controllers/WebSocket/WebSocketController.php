<?php

declare(strict_types=1);

namespace App\Http\Controllers\WebSocket;

class WebSocketController
{
    public function __invoke(): void
    {
        echo 1;
    }
}

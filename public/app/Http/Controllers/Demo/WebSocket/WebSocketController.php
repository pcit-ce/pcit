<?php

declare(strict_types=1);

namespace App\Http\Controllers\Demo\WebSocket;

class WebSocketController
{
    public function __invoke(): void
    {
        echo 1;
    }

    public function client()
    {
        return view('websocket/index.html');
    }
}

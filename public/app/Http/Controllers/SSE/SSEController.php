<?php

declare(strict_types=1);

namespace App\Http\Controllers\SSE;

class SSEController
{
    public function __invoke(): void
    {
        header('X-Accel-Buffering: no');
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        ob_end_clean();

        while (1) {
            $time = date('Y-m-d H:i:s', time());
            // id event retry data \n\n
            echo "id: 1\nretry: 100\ndata: $time\n\n";
            ob_flush();
            flush();
            sleep(1);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Demo\SSE;

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
            // retry：最大间隔时间，整数，单位毫秒
            // event: 自定义信息类型
            echo "id: 1\nretry: 1000\ndata: $time\n\n";
            ob_flush();
            flush();
            sleep(1);
        }
    }

    public function client()
    {
        return view('sse/index.html');
    }
}

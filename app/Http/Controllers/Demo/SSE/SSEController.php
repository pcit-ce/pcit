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

        // 获取请求头 Last-Event-ID: N
        $id = \Request::getHeader('Last-Event-Id', 0);

        while (1) {
            $id = ++$id;
            $time = date('Y-m-d H:i:s', time());
            // id event retry data \n\n

            // retry：最大间隔时间，整数，单位毫秒
            // 浏览器默认的是，如果服务器端三秒内没有发送任何信息，则开始重连。
            // 服务器端可以用retry头信息，指定通信的最大间隔时间。

            // event: 自定义信息类型
            echo "id: $id\nretry: 1000\ndata: time\ndata: $time $id\n\n";
            if (20 === $id) {
                echo "id: $id\nevent: close\nretry: 1000\ndata: time\ndata: $time $id\n\n";
            }
            //ob_flush();
            flush();
            sleep(1);
            if (0 === $id % 2) {
                exit;
            }
        }
    }

    public function client()
    {
        return view('sse/index.html');
    }
}

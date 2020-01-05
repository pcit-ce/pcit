<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

class Healthz
{
    public function __invoke()
    {
        // mysql
        // redis
        // docker
        $content = 'ok';

        return \Response::make($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}

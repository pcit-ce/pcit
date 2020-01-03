<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use Symfony\Component\HttpFoundation\Response;

class Healthz
{
    public function __invoke()
    {
        // mysql
        // redis
        // docker
        $content = 'ok';

        return new Response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}

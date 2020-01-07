<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

class Healthz
{
    public function __invoke()
    {
        return $this->ok();
    }

    public function cache()
    {
        try {
            \Cache::ping();
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }

        return $this->ok();
    }

    public function database()
    {
        return $this->ok();
    }

    public function ok($content = 'ok')
    {
        return \Response::make($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    public function fail($content = 'fail')
    {
        return \Response::make($content, 500, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use PCIT\Framework\Support\DB;

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

            return $this->ok();
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function database()
    {
        try {
            DB::statement('use '.config('database.connections.mysql.database'));

            return $this->ok();
        } catch (\Throwable $e) {
            return $this->fail();
        }
    }

    public function docker()
    {
        try {
            app('pcit')->docker->system->ping();

            return $this->ok();
        } catch (\Throwable $e) {
            return $this->fail();
        }
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use PCIT\Framework\Support\DB;

class Healthz
{
    public function __invoke()
    {
        $cache_result = $this->cache(true);
        $database_result = $this->database(true);
        $docker_result = $this->docker(true);

        if ($cache_result and $database_result and $docker_result) {
            return $this->ok();
        }

        return $this->fail();
    }

    public function cache(bool $returnBool = false)
    {
        try {
            \Cache::ping();

            return $this->ok($returnBool);
        } catch (\Throwable $e) {
            return $this->fail($returnBool);
        }
    }

    public function database(bool $returnBool = false)
    {
        try {
            DB::statement('use '.config('database.connections.mysql.database'));

            return $this->ok($returnBool);
        } catch (\Throwable $e) {
            return $this->fail($returnBool);
        }
    }

    public function docker(bool $returnBool = false)
    {
        try {
            app('pcit')->docker->system->ping();

            return $this->ok($returnBool);
        } catch (\Throwable $e) {
            return $this->fail($returnBool);
        }
    }

    public function ok(bool $returnBool = false)
    {
        if ($returnBool) {
            return true;
        }
        $content = 'ok';

        return \Response::make($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    public function fail(bool $returnBool = false)
    {
        if ($returnBool) {
            return false;
        }
        $content = 'fail';

        return \Response::make($content, 500, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}

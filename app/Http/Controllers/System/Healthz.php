<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use PCIT\Framework\Support\DB;

class Healthz
{
    @@\Route('get', 'api/healthz')
    @@\Route('get', 'api/readyz')
    @@\Route('get', 'api/livez')
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

    @@\Route('get', 'api/healthz/cache')
    @@\Route('get', 'api/readyz/cache')
    @@\Route('get', 'api/livez/cache')
    public function cache(bool $returnBool = false)
    {
        try {
            \Cache::ping();

            return $this->ok($returnBool);
        } catch (\Throwable $e) {
            return $this->fail($returnBool);
        }
    }

    @@\Route('get', 'api/healthz/database')
    @@\Route('get', 'api/readyz/database')
    @@\Route('get', 'api/livez/database')
    public function database(bool $returnBool = false)
    {
        try {
            DB::statement('use '.config('database.connections.mysql.database'));

            return $this->ok($returnBool);
        } catch (\Throwable $e) {
            return $this->fail($returnBool);
        }
    }

    @@\Route('get', 'api/healthz/docker')
    @@\Route('get', 'api/readyz/docker')
    @@\Route('get', 'api/livez/docker')
    public function docker(bool $returnBool = false)
    {
        try {
            \PCIT::docker()->system->ping();

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

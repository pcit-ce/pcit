<?php

declare(strict_types=1);

namespace PCIT\Foundation\Testing;

use PCIT\Support\Request;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function request(string $uri,
                            string $method = 'GET',
                            array $parameters = [],
                            array $cookies = [],
                            array $files = [],
                            array $server = [],
                            $content = null)
    {
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        $request->overrideGlobals();

        $app = require base_path().'framework/bootstrap/app.php';

        $kernel = $app->make(\App\Http\Kernel::class);

        $response = $kernel->handle($request);

        return $response;
    }

    public function get(string $uri, array $headers = [])
    {
        return $this->request($uri, 'GET', $headers);
    }
}

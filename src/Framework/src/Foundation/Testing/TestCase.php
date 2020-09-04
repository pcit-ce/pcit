<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation\Testing;

use PCIT\Framework\Http\Request;
use PCIT\Framework\Http\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public $app;

    public function setUp(): void
    {
        if (!$this->app) {
            $this->app = $this->createApplication();
        }
    }

    /**
     * Creates a Request based on a given URI and configuration.
     *
     * The information contained in the URI always take precedence
     * over the other information (server and parameters).
     *
     * @param string               $uri        The URI
     * @param string               $method     The HTTP method
     * @param array                $parameters The query (GET) or request (POST) parameters
     * @param array                $cookies    The request cookies ($_COOKIE)
     * @param array                $files      The request files ($_FILES)
     * @param array                $server     The server parameters ($_SERVER)
     * @param null|resource|string $content    The raw body data
     *
     * @return Response
     */
    public function request(
        string $uri,
        string $method = 'GET',
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        $request = Request::create($uri, $method, $parameters, $cookies, $files, $server, $content);

        $request->overrideGlobals();

        $kernel = $this->app->make(\App\Http\Kernel::class);

        /** @var \PCIT\Framework\Http\Response */
        return $kernel->handle($request);
    }

    public function get(string $uri, array $headers = [])
    {
        return $this->request($uri, 'GET', $headers);
    }
}

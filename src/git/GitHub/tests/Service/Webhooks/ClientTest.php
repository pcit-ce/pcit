<?php

declare(strict_types=1);

namespace PCIT\GitHub\Tests\Service\Webhooks;

use PCIT\Framework\Http\Request;
use PCIT\PCIT;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var PCIT
     */
    public $pcit;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->pcit = app('pcit');
        parent::setUp();
    }

    /**
     * @param $event
     *
     * @throws \Exception
     */
    public function common($event): void
    {
        $algo = 'sha1';

        $request_body = file_get_contents(__DIR__.'/../../webhooks/github/'.$event.'.json');

        $secret = hash_hmac($algo, $request_body, env('CI_WEBHOOKS_TOKEN'));

        $request = Request::create('/', 'POST', [], [], [],
            [
                'HTTP_X-Github-Event' => $event,
                'HTTP_X-Hub-Signature' => 'sha1='.$secret,
                'REQUEST_TIME_FLOAT' => microtime(true),
            ],
            $request_body
        );

        $request->overrideGlobals();

        $this->app->singleton('request', $request);

        $result = $this->pcit->webhooks->server();

        $this->assertStringMatchesFormat('%s', (string) $result);
    }

    /**
     * @throws \Exception
     */
    public function testPush(): void
    {
        $event = 'push';

        $this->common($event);
    }
}

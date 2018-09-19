<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Webhooks;

use KhsCI\KhsCI as PCIT;
use KhsCI\Support\Request;
use PCIT\Tests\PCITTestCase;

class GitHubClientTest extends PCITTestCase
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
        $this->pcit = $this->getTest();
    }

    /**
     * @param $event
     *
     * @throws \Exception
     */
    public function common($event): void
    {
        $algo = 'sha1';

        $request_body = file_get_contents(__DIR__.'/../../../webhooks/github/'.$event.'.json');

        $secret = hash_hmac($algo, $request_body, 'pcit');

        $request = Request::create('/', 'POST', [], [], [],
            [
                'HTTP_X-Github-Event' => $event,
                'HTTP_X-Hub-Signature' => 'sha1='.$secret,
            ],
            $request_body
        );

        $request->overrideGlobals();

        $this->pcit->request = $request;

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

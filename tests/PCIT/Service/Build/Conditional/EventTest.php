<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Conditional;

use PCIT\Service\Build\Conditional\Event;
use PCIT\Tests\PCITTestCase;

class EventTest extends PCITTestCase
{
    /**
     * @throws \Exception
     */
    public function test(): void
    {
        $result = (new Event('push', 'push'))->handle();
        $this->assertTrue($result);

        $result = (new Event('tag', 'tag'))->handle();
        $this->assertTrue($result);

        $result = (new Event('pull_request', 'pull_request'))->handle();
        $this->assertTrue($result);

        $result = (new Event(null, 'push'))->handle();
        $this->assertTrue($result);

        $result = (new Event(['push'], 'push'))->handle();
        $this->assertTrue($result);

        $result = (new Event(['push', 'tag', 'pull_request'], 'push'))->handle();
        $this->assertTrue($result);

        $result = (new Event(['tag', 'pull_request'], 'push'))->handle();
        $this->assertFalse($result);

        $result = (new Event(['tag'], 'push'))->handle();
        $this->assertFalse($result);
    }
}

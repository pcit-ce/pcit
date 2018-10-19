<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Conditional;

use PCIT\Service\Build\Conditional\Platform;
use PCIT\Tests\PCITTestCase;

class PlatformTest extends PCITTestCase
{
    /**
     * @throws \Exception
     */
    public function test(): void
    {
        $result = (new Platform('linux/*', 'linux/amd64'))->regHandle();
        $this->assertTrue($result);

        $result = (new Platform('windows/*', 'linux/amd64'))->regHandle();
        $this->assertFalse($result);
    }

    /**
     * @throws \Exception
     */
    public function testArray(): void
    {
        $result = (new Platform(['linux/*'], 'linux/amd64'))->regHandle();
        $this->assertTrue($result);

        $result = (new Platform(['windows/*'], 'linux/amd64'))->regHandle();
        $this->assertFalse($result);

        $result = (new Platform(null, 'linux/amd64'))->regHandle();
        $this->assertTrue($result);
    }
}

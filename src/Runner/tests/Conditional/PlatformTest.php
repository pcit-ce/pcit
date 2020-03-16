<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Conditional;

use PCIT\Runner\Conditional\Platform;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test(): void
    {
        $result = (new Platform('linux/*', 'linux/amd64'))->handle(true);
        $this->assertTrue($result);

        $result = (new Platform('windows/*', 'linux/amd64'))->handle(true);
        $this->assertFalse($result);
    }

    /**
     * @throws \Exception
     */
    public function testArray(): void
    {
        $result = (new Platform(['linux/*'], 'linux/amd64'))->handle(true);
        $this->assertTrue($result);

        $result = (new Platform(['windows/*'], 'linux/amd64'))->handle(true);
        $this->assertFalse($result);

        $result = (new Platform(null, 'linux/amd64'))->handle(true);
        $this->assertTrue($result);
    }
}

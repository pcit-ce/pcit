<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Conditional;

use PCIT\Runner\Conditional\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test(): void
    {
        $result = (new Tag('^[0-9.]+$', '1.2.0'))->regHandle();
        $this->assertTrue($result);

        $result = (new Tag('^[0-9.]+$', '1.2.0-rc'))->regHandle();
        $this->assertFalse($result);

        $result = (new Tag('^[0-9.]+', '1.2.0-rc'))->regHandle();
        $this->assertTrue($result);

        $result = (new Tag('^[0-9.]+', 'v1.2.0'))->regHandle();
        $this->assertFalse($result);

        $result = (new Tag('^v([0-9.]+)$', 'v1.2.0'))->regHandle();
        $this->assertTrue($result);

        $result = (new Tag('^v([0-9.]+)$', '1.2.0'))->regHandle();
        $this->assertFalse($result);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Mock;

use PCIT\Builder\Events\Log;
use PCIT\Tests\PCITTestCase;

class MockTest extends PCITTestCase
{
    /**
     * 请注意，final、private 和 static 方法无法对其进行上桩(stub)或模仿(mock).
     */
    public function testStub(): void
    {
        $stub = $this->createMock(Log::class);

        $stub->method('handle')->willReturn([1, 100]);

        $this->assertEquals([1, 100], $stub->handle());
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Tests\Mock;

use App\Console\Build;
use KhsCI\Tests\KhsCITestCase;

class MockTest extends KhsCITestCase
{
    /**
     * 请注意，final、private 和 static 方法无法对其进行上桩(stub)或模仿(mock).
     *
     * @throws \ReflectionException
     */
    public function testStub(): void
    {
        $stub = $this->createMock(Build::class);

        $stub->method('test')->willReturn(1);

        $this->assertEquals(1, $stub->test());
    }
}

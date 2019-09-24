<?php

declare(strict_types=1);

namespace PCIT\Tests;

use PCIT\Support\CI;
use Tests\TestCase;

class PCITTest extends TestCase
{
    public function test(): void
    {
        $this->assertTrue(CI::environment('testing'));
    }
}

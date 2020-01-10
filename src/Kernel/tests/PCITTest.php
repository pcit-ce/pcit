<?php

declare(strict_types=1);

namespace PCIT\Tests;

use Tests\TestCase;

class PCITTest extends TestCase
{
    public function test(): void
    {
        $this->assertTrue(\App::environment('testing'));
    }
}

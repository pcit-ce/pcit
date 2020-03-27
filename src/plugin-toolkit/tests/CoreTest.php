<?php

declare(strict_types=1);

namespace PCIT\Plugin\Toolkit\Tests;

use PCIT\Plugin\Toolkit\Core;
use Tests\TestCase;

class CoreTest extends TestCase
{
    /**
     * @var Core
     */
    public $core;

    public function setUp(): void
    {
        $this->core = new Core();
    }

    public function test_isDebug(): void
    {
        $this->assertFalse($this->core->isDebug());

        putenv('PCIT_STEP_DEBUG=true');
        $this->assertTrue($this->core->isDebug());
    }
}

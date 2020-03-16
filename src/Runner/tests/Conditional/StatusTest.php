<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Conditional;

use PCIT\Runner\Conditional\Status;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function test(): void
    {
        $result = (new Status(null, 'failure'))->handle();
        $this->assertFalse($result);

        $result = (new Status('failure', 'failure'))->handle();
        $this->assertTrue($result);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Events\EnvHandler;
use PHPUnit\Framework\TestCase;

class EnvHandlerTest extends TestCase
{
    public function test_arrayHandler(): void
    {
        $result = (new EnvHandler())->arrayHandler('a');

        // var_dump($result);

        $this->assertEquals('a', $result);

        $result = (new EnvHandler())->arrayHandler(['a', 'b']);

        // var_dump($result);

        $this->assertEquals('a,b', $result);
    }
}

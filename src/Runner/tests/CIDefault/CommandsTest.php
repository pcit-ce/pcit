<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\CIDefault;

use PCIT\Builder\CIDefault\Commands;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test(): void
    {
        $result = Commands::get('php', 'sami');

        $this->assertArrayHasKey('0', $result);
    }
}

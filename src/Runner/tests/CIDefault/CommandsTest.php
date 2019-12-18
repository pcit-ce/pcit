<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\CIDefault;

use PCIT\Runner\CIDefault\Commands;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test(): void
    {
        $result = Commands::get('php', 'sami');

        $this->assertArrayHasKey('0', $result);
    }
}

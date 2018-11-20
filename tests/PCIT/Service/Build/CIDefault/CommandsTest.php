<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\CIDefault;

use PCIT\Service\Build\CIDefault\Commands;
use PCIT\Tests\PCITTestCase;

class CommandsTest extends PCITTestCase
{
    public function test(): void
    {
        $result = Commands::get('php', 'sami');

        $this->assertArrayHasKey('0', $result);
    }
}

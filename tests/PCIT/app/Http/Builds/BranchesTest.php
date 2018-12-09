<?php

declare(strict_types=1);

use PCIT\Tests\PCITTestCase;

class BranchesTest extends PCITTestCase
{
    public function test(): void
    {
        $response = $this->get('/dashboard');

        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }
}

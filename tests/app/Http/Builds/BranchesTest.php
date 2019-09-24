<?php

declare(strict_types=1);

use Tests\TestCase;

class BranchesTest extends TestCase
{
    public function test(): void
    {
        $response = $this->get('/dashboard');

        $this->assertArrayHasKey('message', json_decode($response->getContent(), true));
    }
}

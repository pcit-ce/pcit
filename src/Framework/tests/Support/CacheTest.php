<?php

declare(strict_types=1);

namespace PCIT\Framework\Tests\Support;

use Tests\TestCase;

class CacheTest extends TestCase
{
    public function test(): void
    {
        \Cache::store()->set('key', 'value');

        $this->assertEquals('value', \Cache::store()->get('key'));
    }
}

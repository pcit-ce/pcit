<?php

declare(strict_types=1);

namespace PCIT\Framework\Tests\Support;

use Tests\TestCase;

class CacheTest extends TestCase
{
    public function test(): void
    {
        \Cache::set('key', 'value');

        $this->assertEquals('value', \Cache::get('key'));
    }
}

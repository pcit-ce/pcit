<?php

namespace KhsCI\Tests\CLI;

use Exception;
use KhsCI\Tests\KhsCITestCase;

class Up extends KhsCITestCase
{
    /**
     * @throws Exception
     */
    public function testSkip()
    {
        $up = new \App\Console\Up();

        $bool = $up->skip('', 1);

        $this->assertEquals(false, $bool);

        $bool = $up->skip('commit message [skip ci]', 1);

        $this->assertEquals(true, $bool);

        $bool = $up->skip('', 1, 'master',
            '{"branches": {"exclude": ["dev*"],"include": ["master"]}}');

        $this->assertEquals(false, $bool);

        $bool = $up->skip('', 1, 'dev',
            '{"branches": {"exclude": ["de*"],"include": ["master"]}}');

        $this->assertEquals(true, $bool);
    }
}

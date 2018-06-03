<?php

namespace KhsCI\Tests\CLI;

use Exception;
use KhsCI\Tests\KhsCITestCase;

class Up extends KhsCITestCase
{
    /**
     * @var \App\Console\Up
     */
    private $up;

    protected function setUp()
    {
        $this->up = new \App\Console\Up();
    }

    /**
     * @throws Exception
     */
    public function testSkip()
    {
        $up = $this->up;

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

    /**
     * @throws Exception
     */
    public function testGetConfig()
    {
        $output = $this->up->getConfig(
            null,
            'https://raw.githubusercontent.com/khs1994/khs1994.github.io/hexo/_config.yml2'
        );

        $this->assertEquals([], $output);

        $output = $this->up->getConfig(
            null,
            'https://raw.githubusercontent.com/khs1994-docker/lnmp/master/.khsci.yml'
        );

        $this->assertArrayHasKey('pipeline', $output);
    }

}

<?php

declare(strict_types=1);

namespace KhsCI\Tests\CLI;

use Exception;
use KhsCI\Tests\KhsCITestCase;

class UpTest extends KhsCITestCase
{
    /**
     * @var \App\Console\Up
     */
    private $up;

    protected function setUp(): void
    {
        $this->up = new \App\Console\Up();
    }

    /**
     * @throws Exception
     */
    public function testSkip(): void
    {
        $up = $this->up;

        $bool = $up->skip('', 1);

        $this->assertFalse($bool);

        $bool = $up->skip('commit message [skip ci]', 1);

        $this->assertTrue($bool);

        $bool = $up->skip('', 1, 'master',
            '{"branches": {"exclude": ["dev*"],"include": ["master"]}}');

        $this->assertFalse($bool);

        $bool = $up->skip('', 1, 'dev',
            '{"branches": {"exclude": ["de*"],"include": ["master"]}}');

        $this->assertTrue($bool);
    }

    /**
     * @throws Exception
     */
    public function testGetConfig(): void
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

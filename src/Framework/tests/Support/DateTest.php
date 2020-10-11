<?php

declare(strict_types=1);

namespace PCIT\Framework\Tests\Support;

use PCIT\Framework\Support\Date;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class DateTest extends TestCase
{
    public function testParse(): void
    {
        $date = [
            '2018-05-02T03:55:52Z',
            '2018-05-02T12:02:20+08:00',
        ];

        foreach ($date as $item) {
            $this->assertEquals(Date::parse($item), Yaml::parse($item));
        }
    }
}

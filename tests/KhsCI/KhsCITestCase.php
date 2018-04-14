<?php

declare(strict_types=1);

namespace KhsCI\Tests;

use KhsCI\KhsCI;
use PHPUnit\Framework\TestCase;

class KhsCITestCase extends TestCase
{
    private static $test;

    public static function getTest()
    {
        if (!(self::$test instanceof KhsCI)) {
            self::$test = new KhsCI();
        }

        return self::$test;
    }
}

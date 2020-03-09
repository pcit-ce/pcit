<?php

declare(strict_types=1);

namespace Tests;

use PCIT\Framework\Foundation\Testing\TestCase as BaseTestCase;
use PCIT\PCIT;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private static $test;

    /**
     * @param string $git_type
     *
     * @return PCIT
     *
     * @throws \Exception
     */
    public static function getTest(array $config = [], string $git_type = null)
    {
        if (!(self::$test instanceof PCIT)) {
            self::$test = new PCIT($config, $git_type ?? 'github');
        }

        return self::$test;
    }
}

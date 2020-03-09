<?php

declare(strict_types=1);

namespace Tests;

use PCIT\Framework\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}

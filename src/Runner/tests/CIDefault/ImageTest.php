<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\CIDefault;

use PCIT\Runner\CIDefault\Image;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function test(): void
    {
        $result = Image::get('php');

        $this->assertEquals('khs1994/php:7.4.4-composer-alpine', $result);
    }
}

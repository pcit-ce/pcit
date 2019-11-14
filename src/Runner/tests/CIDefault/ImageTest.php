<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\CIDefault;

use PCIT\Builder\CIDefault\Image;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function test(): void
    {
        $result = Image::get('php');

        $this->assertEquals('khs1994/php:7.3.11-composer-alpine', $result);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\CIDefault;

use PCIT\Builder\CIDefault\Image;
use Tests\PCITTestCase;

class ImageTest extends PCITTestCase
{
    public function test(): void
    {
        $result = Image::get('php');

        $this->assertEquals('khs1994/php:7.3.9-composer-alpine', $result);
    }
}

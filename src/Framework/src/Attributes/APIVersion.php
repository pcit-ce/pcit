<?php

declare(strict_types=1);

namespace PCIT\Framework\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class APIVersion
{
    public function __construct(string $version = 'v1')
    {
    }
}

<?php

namespace PCIT\Framework\Attributes;

@@\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)
class Route
{
    public function __construct(string $method, ...$args)
    {
        \Route::$method(...$args);
    }
}

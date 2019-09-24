<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation;

class AliasLoader
{
    public static function load(?array $alias): void
    {
        foreach ($alias as $key => $value) {
            if (!class_exists($value) or class_exists($key)) {
                continue;
            }
            class_alias($value, $key);
        }
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Builder\CIDefault;

class Status
{
    public static $array;

    public static function default(): void
    {
        self::$array = [
            'after_failure' => 'failure',
            'after_success' => 'success',
            'after_changed' => 'changed',
        ];
    }

    public static function get(?string $pipeline)
    {
        self::default();

        return self::$array[$pipeline] ?? null;
    }
}

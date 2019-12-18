<?php

declare(strict_types=1);

namespace PCIT\Runner\CIDefault;

class Status
{
    public static $statusList;

    public static function default(): void
    {
        self::$statusList = [
            'after_failure' => 'failure',
            'after_success' => 'success',
            'after_changed' => 'changed',
        ];
    }

    public static function get(?string $pipeline)
    {
        self::default();

        return self::$statusList[$pipeline] ?? null;
    }
}

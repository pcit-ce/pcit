<?php

declare(strict_types=1);

namespace PCIT\Builder\CIDefault;

class Commands
{
    public static $array = [
        'php' => [
            'sami' => ['sami update .sami.php'],
            'test' => ['./vendor/bin/phpunit'],
            'install' => ['composer install'],
        ],
        'node_js' => [
            'script' => ['npm test'],
        ],
    ];

    /**
     * @param string $language_type example: php
     * @param string $pipeline      example: script
     *
     * @return array|null
     */
    public static function get(?string $language_type, ?string $pipeline)
    {
        return self::$array[$language_type][$pipeline] ?? null;
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Builder\CIDefault;

class Commands
{
    public static $commandList = [
        'php' => [
            'sami' => ['sami update .sami.php'],
            'install' => ['composer install'],
            'script' => ['composer test'],
        ],
        'node_js' => [
            'install' => ['npm install'],
            'script' => ['npm test'],
        ],
    ];

    /**
     * @param string $language_type example: php
     * @param string $pipeline      example: script
     *
     * @return array
     */
    public static function get(?string $language_type, ?string $pipeline): array
    {
        return self::$commandList[$language_type][$pipeline] ?? [];
    }
}

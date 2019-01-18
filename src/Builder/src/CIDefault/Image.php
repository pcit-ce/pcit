<?php

declare(strict_types=1);

namespace PCIT\Builder\CIDefault;

use PCIT\Exception\PCITException;

class Image
{
    public static $array = [
        'node_js' => 'node:11.6.0-alpine',
        'php' => 'khs1994/php:7.2.14-composer-alpine',
        'bash' => 'bash',
        'sh' => 'alpine',
    ];

    /**
     * @param string|null $language_type example: php
     *
     * @return string
     *
     * @throws PCITException
     */
    public static function get(?string $language_type)
    {
        if (null === $language_type) {
            throw new PCITException('You must define pipeline image,or define language');
        }

        return self::$array[$language_type] ?? 'bash';
    }
}

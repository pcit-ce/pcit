<?php

declare(strict_types=1);

namespace PCIT\Builder\CIDefault;

use Symfony\Component\Yaml\Yaml;

class Commands
{
    /**
     * @param string $language_type example: php
     * @param string $pipeline      example: script
     */
    public static function get(?string $language_type, ?string $pipeline): array
    {
        $commandList = Yaml::parse(file_get_contents(__DIR__.'/manifest.yaml'))['run'];

        return $commandList[$language_type][$pipeline] ?? [];
    }
}

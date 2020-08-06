<?php

declare(strict_types=1);

namespace PCIT\Runner\CIDefault;

use PCIT\Exception\PCITException;
use Symfony\Component\Yaml\Yaml;

class Image
{
    /**
     * @param null|string $language_type example: php
     *
     * @throws PCITException
     *
     * @return string
     */
    public static function get(?string $language_type)
    {
        if (null === $language_type) {
            throw new PCITException('You must define pipeline image or language');
        }

        $imageList = Yaml::parse(file_get_contents(__DIR__.'/manifest.yaml'))['image'];

        return $imageList[$language_type] ?? 'bash';
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Interfaces;

interface ServiceInterface
{
    public static function handle(): array;
}

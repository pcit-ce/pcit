<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Config
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get($name)
    {
        return $this->config[$name] ?? [];
    }

    public static function makeOAuthCodingArray($clientId, $clientSecret, $uri)
    {
        return [];
    }
}

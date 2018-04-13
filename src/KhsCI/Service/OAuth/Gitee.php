<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

class Gitee implements OAuth
{
    public function __construct($config, Curl $curl)
    {
    }

    public function getLoginUrl(?string $state): void
    {
    }

    public function getAccessToken(string $code, ?string $state): void
    {
    }
}

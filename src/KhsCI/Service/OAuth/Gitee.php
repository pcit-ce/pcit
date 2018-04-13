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

    public static function getUserInfo(string $accessToken, bool $raw = false): void
    {
    }

    public static function getProjects(string $accessToken, int $page = 1, bool $raw = false): void
    {
    }

    public static function getWebhooks(string $accessToken, string $username, string $project, bool $raw): void
    {
    }
}

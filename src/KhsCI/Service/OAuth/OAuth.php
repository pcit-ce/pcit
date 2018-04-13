<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

interface OAuth
{
    public function __construct($config, Curl $curl);

    public function getLoginUrl(?string $state);

    public function getAccessToken(string $code, ?string $state);

    public static function getUserInfo(string $accessToken, bool $raw = false);

    public static function getProjects(string $accessToken, int $page = 1, bool $raw = false);

    public static function getWebhooks(string $accessToken, string $username, string $project, bool $raw);
}

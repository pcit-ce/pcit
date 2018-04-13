<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

interface OAuth
{
    public function __construct($config, Curl $curl, $scope = null);

    public function getLoginUrl();

    public function getAccessToken(string $code);
}

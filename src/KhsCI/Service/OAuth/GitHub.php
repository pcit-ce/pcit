<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

class GitHub implements OAuth
{
    const URL = '';

    public function __construct($config, Curl $curl, $scope = null)
    {

    }

    public function getLoginUrl()
    {

    }

    public function getAccessToken(string $code)
    {

    }

}

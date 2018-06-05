<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

interface OAuthInterface
{
    public function __construct($config, Curl $curl);

    /**
     * @param null|string $state
     *
     * @return mixed
     */
    public function getLoginUrl(?string $state);

    /**
     * @param string      $code
     * @param null|string $state
     * @param bool        $raw
     *
     * @return mixed
     */
    public function getAccessToken(string $code, ?string $state, bool $raw = false);
}

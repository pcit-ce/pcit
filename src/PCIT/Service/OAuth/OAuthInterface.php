<?php

declare(strict_types=1);

namespace PCIT\Service\OAuth;

use Curl\Curl;

interface OAuthInterface
{
    public function __construct($config, Curl $curl);

    /**
     * @param null|string $state
     *
     * @return string
     */
    public function getLoginUrl(?string $state): string;

    /**
     * @param string      $code
     * @param null|string $state
     * @param bool        $raw
     *
     * @return array
     */
    public function getAccessToken(string $code, ?string $state, bool $raw = false): array;
}

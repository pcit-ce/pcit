<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

interface OAuth
{
    public function getLoginUrl();

    public function getAccessToken(string $code);
}

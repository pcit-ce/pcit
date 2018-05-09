<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Firebase\JWT\JWT as JWTService;

class JWT
{
    /**
     * @param string $private_key_path
     * @param        $iss
     *
     * @return string
     *
     * @see https://developer.github.com/apps/building-github-apps/authentication-options-for-github-apps/#authenticating-as-a-github-app
     */
    public static function getJWT(string $private_key_path, int $iss)
    {
        $privateKey = file_get_contents($private_key_path);

        $token = [
            'iss' => $iss,
            'iat' => time(),
            'exp' => time() + 10 * 60,
        ];

        $jwt = JWTService::encode($token, $privateKey, 'RS256');

        return $jwt;
    }
}

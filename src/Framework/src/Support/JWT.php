<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

use Firebase\JWT\JWT as JWTService;

class JWT
{
    /**
     * PCIT 加密 token.
     */
    public static function encode(string $privateKey, string $git_type, string $username, int $uid, int $exp = null): string
    {
        $privateKey = file_get_contents($privateKey);

        $ci_host = config('app.host');

        $token = [
            'iss' => $ci_host,
            'iat' => time(),
            'exp' => $exp ?? (time() + 60 * 10),
            'aud' => $ci_host,
            'username' => $username,
            'git_type' => $git_type,
            'uid' => $uid,
        ];

        return JWTService::encode($token, $privateKey, 'RS256');
    }

    /**
     * 解密 token.
     *
     * @param resource $publicKey returned by openssl_get_publickey()
     *
     * @return object
     */
    public static function decode(string $jwt, $publicKey)
    {
        return JWTService::decode($jwt, $publicKey, ['RS256']);
    }
}

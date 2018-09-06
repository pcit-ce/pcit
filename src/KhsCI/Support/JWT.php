<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Firebase\JWT\JWT as JWTService;

class JWT
{
    /**
     * GitHub App 由 JWT expire 10m 获取 Token expire 60m.
     *
     * @param string $private_key_path
     * @param int    $iss
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

    /**
     * KhsCI 加密 token.
     *
     * @param string $privateKey
     * @param string $git_type
     * @param string $username
     * @param int    $uid
     * @param int    $exp
     *
     * @return string
     */
    public static function encode(string $privateKey, string $git_type, string $username, int $uid, int $exp = null)
    {
        $privateKey = file_get_contents($privateKey);

        $ci_host = Env::get('CI_HOST');

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
     * @param string $jwt
     * @param string $publicKey
     *
     * @return object
     */
    public static function decode(string $jwt, string $publicKey)
    {
        $publicKey = file_get_contents($publicKey);

        $obj = JWTService::decode($jwt, $publicKey, ['RS256']);

        return $obj;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ApiToken;
use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use KhsCI\Support\JWT;
use KhsCI\Support\Request;

class APITokenController
{
    /**
     * @param int $build_key_id
     *
     * @return bool
     * @throws Exception
     */
    public static function check(int $build_key_id)
    {
        $token = Request::getHeader('Authorization');

        $token && $token = explode(' ', $token)[1] ?? null;

        if (!$token) {
            throw new Exception('Requires authentication', 401);
        }

        return true;
    }

    public static function checkByRepo(string $git_type, string $username, string $repo_name)
    {

    }

    public static function checkByUser(string $git_type, string $username)
    {

    }

    /**
     * 生成 API Token.
     *
     * @return string
     *
     * @throws Exception
     */
    public function find()
    {
        $json = file_get_contents('php://input');

        $obj = json_decode($json);

        $git_type = $obj->git_type ?? false;
        $username = $obj->username ?? false;
        $password = $obj->password ?? false;

        if (!($git_type && $username && $password)) {
            throw new Exception('Requires authentication', 401);
        }

        $curl = new Curl();

        $curl->setHtpasswd((string) $username, (string) $password);

        $git_obj = json_decode($curl->get('https://api.github.com/user'));

        if (200 !== $curl->getCode()) {
            throw new Exception('Requires authentication', 401);
        }

        $uid = $git_obj->id;
        $git_username = $git_obj->login;

        if ($git_username !== $username) {
            throw new Exception('Requires authentication', 401);
        }

        $jwt = JWT::encode(
            __DIR__.'/../../../public/../private_key/'.Env::get('CI_GITHUB_APP_PRIVATE_FILE'),
            (string) $git_type,
            (string) $username,
            $uid
        );

        $token = hash('sha256', explode('.', $jwt)[1]);

        ApiToken::add($token, (string) $git_type, (int) $uid);

        return $token;
    }
}

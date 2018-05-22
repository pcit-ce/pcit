<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ApiToken;
use App\Build;
use App\Repo;
use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use KhsCI\Support\JWT;
use KhsCI\Support\Request;

class APITokenController
{
    /**
     * @return array|false|mixed|null
     *
     * @throws Exception
     */
    private static function getToken()
    {
        $token = Request::getHeader('Authorization');

        if (!$token) {
            throw new Exception('Requires authentication', 401);
        }

        return $token;
    }

    /**
     * @param int $build_key_id
     *
     * @return bool
     * @throws Exception
     */
    public static function check(int $build_key_id)
    {
        $token = self::getToken();

        $array = ApiToken::getGitTypeAndUid((string) $token);

        $rid = Build::getRid($build_key_id);


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

        $token_from_db = ApiToken::get((string) $git_type, $uid);

        if ($token_from_db) {

            return $token_from_db;
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

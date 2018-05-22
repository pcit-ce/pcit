<?php

namespace App\Http\Controllers;

use App\ApiToken;
use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use KhsCI\Support\JWT;

class APITokenController
{
    /**
     * 生成 API Token
     *
     * @return string
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

        $curl->setHtpasswd($username, $password);

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
            $git_type,
            $username,
            $uid
        );

        $token = hash('sha256', explode('.', $jwt)[1]);

        ApiToken::add($token, $git_type, (int) $uid);

        return $token;
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ApiToken;
use App\Build;
use App\Repo;
use App\User;
use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use KhsCI\Support\Git;
use KhsCI\Support\JWT;
use KhsCI\Support\Request;

class APITokenController
{
    /**
     * 从请求头获取 token.
     *
     * @return string
     *
     * @throws Exception
     */
    private static function getToken()
    {
        try {
            $token = Request::getHeader('Authorization');
            $token = explode(' ', $token)[1] ?? null;
        } catch (\Throwable $e) {
            throw new Exception('Requires authentication', 401);
        }

        if (!$token) {
            throw new Exception('Requires authentication', 401);
        }

        return $token;
    }

    /**
     * @throws Exception
     */
    public static function getUser()
    {
        $token = self::getToken();
        $array = ApiToken::getGitTypeAndUid((string) $token);

        list('git_type' => $git_type, 'uid' => $uid) = $array[0];

        return [$git_type, $uid];
    }

    /**
     * 检查 token 是否有某构建的权限.
     *
     * @param int $build_key_id
     *
     * @return array
     *
     * @throws Exception
     */
    public static function check(int $build_key_id)
    {
        $token = self::getToken();
        $array = ApiToken::getGitTypeAndUid((string) $token);

        list('git_type' => $git_type, 'uid' => $uid) = $array[0];

        // 由构建 ID 得到仓库 ID，及 git 类型
        $rid = Build::getRid($build_key_id);
        $git_type_from_build = Build::getGitType($build_key_id);

        // 若 token git 类型不匹配则 404
        if ($git_type !== $git_type_from_build) {
            throw new Exception('Not Found', 404);
        }

        // 检查 token uid 是否为仓库管理员
        $output = Repo::checkAdmin($git_type, (int) $rid, (int) $uid);

        if ($output) {
            return [$rid, $git_type, $uid];
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * 检查 Token 是否有某仓库的权限.
     *
     * Token 的 uid 是否在给定仓库的管理员列表中
     *
     * @param string $username
     * @param string $repo_name
     *
     * @return array
     *
     * @throws Exception
     */
    public static function checkByRepo(string $username, string $repo_name)
    {
        $token = self::getToken();
        $array = ApiToken::getGitTypeAndUid((string) $token);

        list('git_type' => $git_type, 'uid' => $uid) = $array[0];

        // 上面获取到了 token 的 uid
        $rid = Repo::getRid($git_type, $username, $repo_name);

        // 比对管理员列表
        $output = Repo::checkAdmin($git_type, (int) $rid, (int) $uid);

        if ($output) {
            return [$rid, $git_type, $uid];
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * 获取 token 对应的信息.
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitTypeAndUid()
    {
        return ApiToken::getGitTypeAndUid(self::getToken());
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

        if (!in_array($git_type, Git::SUPPORT_GIT_ARRAY)) {
            throw new Exception('Not Found', 404);
        }

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
            (int) $uid
        );

        $token = hash('sha256', explode('.', $jwt)[1]);

        ApiToken::add($token, (string) $git_type, (int) $uid);

        return $token;
    }
}

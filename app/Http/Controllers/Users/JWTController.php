<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Build;
use App\Repo;
use Curl\Curl;
use Exception;
use PCIT\Framework\Support\JWT;
use PCIT\Support\Git;

class JWTController
{
    /**
     * 从请求头获取 token.
     *
     * @return string
     *
     * @throws \Exception
     */
    private static function getToken()
    {
        $token = \Request::getHeader('Authorization');

        if (!$token || 'token undefined' === $token) {
            throw new Exception('Requires authentication', 401);
        }

        $token = explode(' ', $token)[1] ?? null;

        if ($token) {
            return $token;
        }

        throw new Exception('Requires authentication', 401);
    }

    /**
     * @return resource
     */
    public static function getPublicKeyFromPrivateKey(string $private_key_path)
    {
        $resource = openssl_get_privatekey('file://'.$private_key_path);
        $result = openssl_pkey_get_details($resource);

        return openssl_get_publickey($result['key']);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public static function getUser(bool $returnGitTypeFirst = true)
    {
        $token = self::getToken();

        $private_key_path = config('git.github.app.private_key_path');
        // $public_key_path = base_path().'framework/storage/private_key/public.key';
        $public_key = self::getPublicKeyFromPrivateKey($private_key_path);

        list('git_type' => $git_type, 'uid' => $uid, 'exp' => $exp) = (array) JWT::decode(
            $token, $public_key);

        if ($exp < time()) {
            throw new Exception('JWT Token timeout', 401);
        }

        if (!$returnGitTypeFirst) {
            return [(int) $uid, $git_type];
        }

        return [$git_type, (int) $uid];
    }

    /**
     * 检查 token 是否有某构建的权限.
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function check(int $build_key_id)
    {
        list($git_type, $uid) = self::getUser();

        // 由构建 ID 得到仓库 ID，及 git 类型
        $rid = Build::getRid($build_key_id);
        $git_type_from_build = Build::getGitType($build_key_id);

        // 若 token git 类型不匹配则 404
        if ($git_type !== $git_type_from_build) {
            throw new Exception('Not Found', 404);
        }

        // 检查 token uid 是否为仓库管理员
        $result = Repo::checkAdmin((int) $rid, (int) $uid, false, $git_type);

        if ($result) {
            return [$rid, $git_type, $uid];
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * 检查 Token 是否有某仓库的权限.
     *
     * Token 的 uid 是否在给定仓库的管理员列表中
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function checkByRepo(string $username, string $repo_name)
    {
        list($git_type, $uid) = self::getUser();

        // 上面获取到了 token 的 uid
        $rid = Repo::getRid($username, $repo_name, $git_type);

        // 比对管理员列表
        $result = Repo::checkAdmin((int) $rid, (int) $uid, false, $git_type);

        if ($result) {
            return [(int) $rid, $git_type, (int) $uid];
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * 生成 API Token.
     *
     * @param string $username
     * @param int    $uid
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function generate(string $git_type = null, string $username = null, int $uid = null)
    {
        if ($git_type) {
            goto a;
        }

        $request = app('request');

        // $json = file_get_contents('php://input');
        $json = $request->getContent();

        $obj = json_decode($json);

        $git_type = $obj->git_type ?? false;

        if (!\in_array($git_type, Git::SUPPORT_GIT_ARRAY, true)) {
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
            throw new Exception('Requires authentication, maybe username or password not incorrect', 401);
        }

        $uid = $git_obj->id;
        $git_username = $git_obj->login;

        if ($git_username !== $username) {
            throw new Exception('Requires authentication', 401);
        }

        // 验证通过 返回 jwt
        a:
        $token = JWT::encode(
            config('git.github.app.private_key_path'),
            (string) $git_type,
            (string) $username,
            (int) $uid,
            time() + 100 * 24 * 60 * 60
        );

        return compact('token');
    }
}

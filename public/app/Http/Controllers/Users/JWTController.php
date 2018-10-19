<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Build;
use App\Repo;
use Curl\Curl;
use Exception;
use PCIT\Support\Git;
use PCIT\Support\JWT;
use PCIT\Support\Request;

class JWTController
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
        $token = Request::getHeader('Authorization');
        $token = explode(' ', $token)[1] ?? null;

        if ($token) {
            return $token;
        }

        throw new Exception('Requires authentication', 401);
    }

    /**
     * @param bool $returnGitTypeFirst
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getUser(bool $returnGitTypeFirst = true)
    {
        $token = self::getToken();

        list('git_type' => $git_type, 'uid' => $uid, 'exp' => $exp) = (array) JWT::decode(
            $token,
            __DIR__.'/../../../../storage/private_key/pub.key'
        );

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
     * @param int $build_key_id
     *
     * @return array
     *
     * @throws Exception
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
        $output = Repo::checkAdmin((int) $rid, (int) $uid, $git_type);

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
        list($git_type, $uid) = self::getUser();

        // 上面获取到了 token 的 uid
        $rid = Repo::getRid($username, $repo_name, $git_type);

        // 比对管理员列表
        $output = Repo::checkAdmin((int) $rid, (int) $uid, false, $git_type);

        if ($output) {
            return [(int) $rid, $git_type, (int) $uid];
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * 生成 API Token.
     *
     * @param string|null $git_type
     * @param string      $username
     * @param int         $uid
     *
     * @return string
     *
     * @throws Exception
     */
    public static function generate(string $git_type = null, string $username = null, int $uid = null)
    {
        if ($git_type) {
            goto a;
        }

        $json = file_get_contents('php://input');

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
            throw new Exception('Requires authentication', 401);
        }

        $uid = $git_obj->id;
        $git_username = $git_obj->login;

        if ($git_username !== $username) {
            throw new Exception('Requires authentication', 401);
        }

        // 验证通过 返回 jwt
        a:
        return JWT::encode(
            __DIR__.'/../../../../storage/private_key/'.getenv('CI_GITHUB_APP_PRIVATE_FILE'),
            (string) $git_type,
            (string) $username,
            (int) $uid,
            time() + 100 * 24 * 60 * 60
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\User;
use Exception;

class UserInfoController
{
    /**
     * 获取当前登录用户信息.
     *
     * /user
     *
     * @throws Exception
     */
    public function __invoke()
    {
        list($uid, $git_type) = JWTController::getUser(false);

        return User::getUserInfo(null, (int) $uid, $git_type)[0] ?? [];
    }

    /**
     * 获取某个用户的信息.
     *
     * /user/{git_type}/{username}
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function find(string $git_type, string $username)
    {
        try {
            JWTController::getUser();
        } catch (\Throwable $e) {
            return User::getUserBasicInfo($username, null, $git_type);
        }

        $result = User::getUserInfo($username, null, $git_type)[0] ?? [];

        if ($result) {
            return $result;
        }

        throw new Exception('Not Found', 404);
    }
}

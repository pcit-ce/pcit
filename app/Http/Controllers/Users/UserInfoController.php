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

        return User::getUserInfo(null, (int) $uid, $git_type);
    }

    /**
     * 获取某个用户的信息.
     *
     * /user/{git_type}/{username}
     *
     * @param string $git_type
     * @param string $username
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function find(string $git_type, string $username)
    {
        JWTController::getUser();

        $output = User::getUserInfo($username, 0, $git_type);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 404);
    }
}

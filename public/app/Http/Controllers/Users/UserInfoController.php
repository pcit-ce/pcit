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
        list($git_type, $uid) = JWTController::getUser();

        return User::getUserInfo($git_type, null, (int) $uid);
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

        $output = User::getUserInfo($git_type, $username);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 404);
    }
}

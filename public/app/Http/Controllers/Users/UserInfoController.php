<?php

namespace App\Http\Controllers\Users;

use App\User;
use Exception;

class UserInfoController
{
    /**
     * @param string $git_type
     *
     * @return array|string
     * @throws Exception
     */
    public function find(string $git_type)
    {
        $username = 'khs1994';

        $output = User::getUserInfo($git_type, $username);

        if ($output) {

            return $output;
        }

        throw new Exception('Not Found', 404);
    }
}

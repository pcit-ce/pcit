<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;

class User
{
    /**
     * @param string $git_type
     * @param int    $uid
     * @param string $username
     * @param string $email
     * @param string $pic
     * @param string $accessToken
     *
     * @throws Exception
     */
    public static function updateUserInfo(string $git_type,
                                          int $uid,
                                          string $username,
                                          string $email,
                                          string $pic,
                                          string $accessToken
    ): void {
        $user_key_id = self::exists($git_type, $username);

        if ($user_key_id) {
            $sql = 'UPDATE user SET git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id=?';
            DB::update($sql, [
                    $git_type, $uid, $username, $email, $pic, $accessToken, $user_key_id,
                ]
            );
        } else {
            $sql = 'INSERT user VALUES(null,?,?,?,?,?,?)';
            DB::insert($sql, [$git_type, $uid, $username, $email, $pic, $accessToken]);
        }
    }

    /**
     * @param string $git_type
     * @param string $username
     *
     * @return int
     *
     * @throws Exception
     */
    public static function exists(string $git_type, string $username)
    {
        $sql = 'SELECT id FROM user WHERE username=? AND git_type=?';

        $user_key_id = DB::select($sql, [$username, $git_type], true) ?? false;

        return (int) $user_key_id;
    }
}

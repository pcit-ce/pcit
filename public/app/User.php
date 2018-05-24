<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class User extends DBModel
{
    protected static $table = 'user';

    /**
     * @param string $git_type
     * @param string $username
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getUserInfo(string $git_type, string $username)
    {
        $sql = 'SELECT * FROM user WHERE git_type=? AND username=?';

        return DB::select($sql, [$git_type, $username]);
    }

    /**
     * @param string      $git_type
     * @param int         $uid
     * @param string      $username
     * @param string|null $email
     * @param string      $pic
     * @param string|null $accessToken
     *
     * @throws Exception
     */
    public static function updateUserInfo(string $git_type,
                                          int $uid,
                                          string $username,
                                          ?string $email,
                                          string $pic,
                                          ?string $accessToken
    ): void {
        $user_key_id = self::exists($git_type, $username);

        if ($user_key_id) {
            $sql = 'UPDATE user SET git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id=?';
            DB::update($sql, [
                    $git_type, $uid, $username, $email, $pic, $accessToken, $user_key_id,
                ]
            );
        } else {
            $sql = 'INSERT INTO user VALUES(null,?,?,?,?,?,?)';
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

    /**
     * @param string $git_type
     * @param string $username
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getUid(string $git_type, string $username)
    {
        $sql = 'SELECT uid FROM user WHERE git_type=? and username=?';

        return DB::select($sql, [$git_type, $username], true);
    }
}

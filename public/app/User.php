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
     * @param string      $git_type
     * @param string|null $username
     * @param int         $uid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getUserInfo(string $git_type, ?string $username, int $uid = 0)
    {
        $sql = 'SELECT * FROM user WHERE git_type=? AND username=?';

        if ($uid) {
            $sql = 'SELECT * FROM user WHERE git_type=? AND uid=?';
        }

        return DB::select($sql, [$git_type, $username ?? $uid]);
    }

    /**
     * @param string      $git_type
     * @param int         $uid
     * @param string      $username
     * @param string|null $email
     * @param string|null $pic
     * @param string|null $accessToken
     *
     * @param bool        $org
     *
     * @throws Exception
     */
    public static function updateUserInfo(string $git_type,
                                          int $uid,
                                          string $username,
                                          ?string $email,
                                          ?string $pic,
                                          ?string $accessToken,
                                          bool $org = false): void
    {
        $user_key_id = self::exists($git_type, $username);

        if ($user_key_id) {
            $sql = 'UPDATE user SET git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id=?';
            DB::update($sql, [
                    $git_type, $uid, $username, $email, $pic, $accessToken, $user_key_id,
                ]
            );
        } else {
            $org || $org = null;
            $org && $org = 'org';

            $sql = 'INSERT INTO user VALUES(null,?,?,?,?,?,?,$org,null)';
            DB::insert($sql, [$git_type, $uid, $username, $email, $pic, $accessToken]);
        }
    }

    /**
     * @param string $git_type
     * @param int    $org_id
     * @param        $admin_uid
     *
     * @throws Exception
     */
    public static function setOrgAdmin(string $git_type, int $org_id, int $admin_uid)
    {
        $sql = <<<EOF
UPDATE user SET admin='[]' WHERE git_type=? AND uid=? AND JSON_VALID(admin) IS NULL
EOF;

        DB::update($sql, [$git_type, $org_id]);

        $sql = <<<EOF
UPDATE user SET admin=JSON_MERGE_PRESERVE(admin,?) 

WHERE git_type=? AND uid=? AND NOT JSON_CONTAINS(admin,JSON_QUOTE(?))
EOF;
        DB::update($sql, ["[\"$admin_uid\"]", $git_type, $org_id, $admin_uid]);
    }

    public static function deleteOrgAdmin()
    {

    }

    /**
     * @param string $git_type
     * @param int    $admin_uid
     *
     * @return array|string
     * @throws Exception
     */
    public static function getOrgByAdmin(string $git_type, int $admin_uid)
    {
        $sql = 'SELECT * FROM user WHERE git_type=? AND JSON_CONTAINS(admin,JSON_QUOTE(?)) AND type=?';

        return DB::select($sql, [$git_type, $admin_uid, 'org']);
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

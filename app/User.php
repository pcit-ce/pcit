<?php

declare(strict_types=1);

namespace App;

use PCIT\Framework\Support\DB;
use PCIT\Framework\Support\Model;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Owner;

class User extends Model
{
    protected static $table = 'user';

    /**
     * @param string $git_type
     * @param int    $uid
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function getUserInfo(?string $username, ?int $uid, $git_type = 'github')
    {
        $sql = 'SELECT * FROM user WHERE git_type=? AND username=?';

        if ($uid) {
            $sql = 'SELECT * FROM user WHERE git_type=? AND uid=?';
        }

        return DB::select($sql, [$git_type, $username ?? $uid]);
    }

    /**
     * @param int|Owner $uid
     * @param string    $name
     * @param string    $username
     *
     * @throws \Exception
     */
    public static function updateUserInfo($uid,
                                          ?string $name = null,
                                          string $username = null,
                                          ?string $email = null,
                                          ?string $pic = null,
                                          ?bool $org = false,
                                          string $git_type = 'github'): void
    {
        if ($uid instanceof Owner) {
            $name = $uid->name;
            $username = $uid->username;
            $email = $uid->email;
            $pic = $uid->pic;
            $org = $uid->org;

            $uid = $uid->uid;
        }

        if ($uid instanceof Account) {
            $name = $uid->login;
            $username = $uid->login;
            $email = $uid->email;
            $pic = $uid->avatar_url;
            $org = $uid->org;

            $uid = $uid->id;
        }

        $type = $org ? 'org' : 'user';

        $user_key_id = self::exists($username, $git_type);

        if ($user_key_id) {
            DB::beginTransaction();

            // git_type uid username
            $sql = 'UPDATE user SET git_type=?,uid=?,username=?,type=? WHERE id=?';
            DB::update($sql, [$git_type, $uid, $username, $type, $user_key_id]);

            // name
            $sql = 'UPDATE user SET name=? WHERE id=? AND JSON_QUOTE(?) IS NOT NULL';
            DB::update($sql, [$name, $user_key_id, $name]);

            // email
            $sql = 'UPDATE user SET email=? WHERE id=? AND JSON_QUOTE(?) IS NOT NULL';
            DB::update($sql, [$email, $user_key_id, $email]);

            //pic
            $sql = 'UPDATE user SET pic=? WHERE id=? AND JSON_QUOTE(?) IS NOT NULL';
            DB::update($sql, [$pic, $user_key_id, $pic]);

            DB::commit();

            return;
        }

        $sql = 'INSERT INTO user(git_type, uid, name, username, email, pic, type) VALUES(?,?,?,?,?,?,?)';

        DB::insert($sql, [$git_type, $uid, $name, $username, $email, $pic, $type]);
    }

    /**
     * @throws \Exception
     */
    public static function updateAccessToken(int $uid, string $access_token, string $git_type = 'github'): void
    {
        $sql = 'UPDATE user SET access_token=? WHERE git_type=? AND uid=?';

        DB::insert($sql, [$access_token, $git_type, $uid]);
    }

    /**
     * @throws \Exception
     */
    public static function updateRefreshToken(int $uid, string $refresh_token, string $git_type = 'github'): void
    {
        $sql = 'UPDATE user SET refresh_token=? WHERE git_type=? AND uid=?';

        DB::insert($sql, [$refresh_token, $git_type, $uid]);
    }

    /**
     * @param $admin_uid
     *
     * @throws \Exception
     */
    public static function setOrgAdmin(int $org_id, int $admin_uid, string $git_type = 'github'): void
    {
        $sql = <<<'EOF'
UPDATE user SET org_admin=? WHERE git_type=? AND uid=? AND JSON_VALID(org_admin) IS NULL
EOF;

        DB::update($sql, ['[]', $git_type, $org_id]);

        $sql = <<<'EOF'
UPDATE user SET org_admin=JSON_MERGE_PRESERVE(org_admin,?)

WHERE git_type=? AND uid=? AND NOT JSON_CONTAINS(org_admin,JSON_QUOTE(?))
EOF;
        DB::update($sql, ["[\"$admin_uid\"]", $git_type, $org_id, $admin_uid]);
    }

    public static function deleteOrgAdmin(): void
    {
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public static function getOrgByAdmin(int $admin_uid, string $git_type = 'github')
    {
        $sql = 'SELECT * FROM user WHERE git_type=? AND JSON_CONTAINS(org_admin,JSON_QUOTE(?)) AND type=?';

        return DB::select($sql, [$git_type, $admin_uid, 'org']);
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public static function exists(string $username, string $git_type = 'github')
    {
        $sql = 'SELECT id FROM user WHERE username=? AND git_type=? LIMIT 1';

        $user_key_id = DB::select($sql, [$username, $git_type], true) ?? false;

        return (int) $user_key_id;
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public static function delete(string $org_name, string $git_type = 'github')
    {
        $sql = 'DELETE FROM user WHERE git_type=? AND username=?';

        return DB::delete($sql, [$git_type, $org_name]);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getUid(string $username, string $git_type = 'github')
    {
        $sql = 'SELECT uid FROM user WHERE git_type=? and username=? LIMIT 1';

        return DB::select($sql, [$git_type, $username], true);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function getUsername(int $uid, string $git_type)
    {
        $sql = 'SELECT username FROM user WHERE git_type=? and uid=? LIMIT 1';

        return DB::select($sql, [$git_type, $uid], true);
    }

    /**
     * @throws \Exception
     */
    public static function updateInstallationId(int $installation_id, string $username, string $git_type = 'github'): void
    {
        if (self::exists($username, $git_type)) {
            $sql = 'UPDATE user SET installation_id=? WHERE git_type=? AND username=?';

            DB::update($sql, [$installation_id, $git_type, $username]);

            return;
        }
    }

    /**
     * @param string $username
     *
     * @return array|string
     *
     * @throws \Exception
     */
    public static function getUserBasicInfo(?string $username, ?int $uid, string $git_type = 'github')
    {
        $sql = 'SELECT pic,username FROM user WHERE username=? AND git_type=?';

        if ($uid) {
            $sql = 'SELECT pic,username FROM user WHERE uid=? AND git_type=?';
        }

        return DB::select($sql, [$uid ?? $username, $git_type]);
    }
}

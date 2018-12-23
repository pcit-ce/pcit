<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Support\DB;
use PCIT\Support\Model;

class Env extends Model
{
    protected static $table = 'env_vars';

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return array
     *
     * @throws Exception
     */
    public static function list(int $rid, $git_type = 'github')
    {
        $sql = 'SELECT * FROM env_vars WHERE git_type=? AND rid=?';

        return DB::select($sql, [$git_type, $rid]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param string $name
     * @param string $value
     * @param bool   $public
     *
     * @return int
     *
     * @throws Exception
     */
    public static function create(int $rid, string $name, string $value, bool $public, $git_type = 'github')
    {
        $sql = 'INSERT INTO env_vars VALUES(null,?,?,?,?,?)';

        return DB::insert($sql, [$git_type, $rid, $name, $value, (int) $public]);
    }

    /**
     * @param int    $id
     * @param string $git_type
     * @param int    $rid
     * @param string $value
     * @param bool   $public
     *
     * @return int
     *
     * @throws Exception
     */
    public static function update(int $id, int $rid, string $value, bool $public, $git_type = 'github')
    {
        $sql = 'UPDATE env_vars SET value=? WHERE id=? AND git_type=? AND rid=? AND public=?';

        return DB::update($sql, [$value, $id, $git_type, $rid, $public]);
    }

    /**
     * @param int    $id
     * @param string $git_type
     * @param int    $rid
     *
     * @return int
     *
     * @throws Exception
     */
    public static function delete(int $id, int $rid, $git_type = 'github')
    {
        $sql = 'DELETE FROM env_vars WHERE id=? AND git_type=? AND rid=?';

        return DB::delete($sql, [$id, $git_type, $rid]);
    }

    /**
     * @param int    $id
     * @param string $git_type
     * @param int    $rid
     * @param bool   $show
     *
     * @return string
     *
     * @throws Exception
     */
    public static function get(int $id, int $rid, bool $show = false, $git_type = 'github')
    {
        $sql = 'SELECT name,value,public FROM env_vars WHERE id=? AND git_type=? AND rid=?';

        $result = DB::select($sql, [$id, $git_type, $rid]);

        if (!$result) {
            throw new Exception('Not Found', 404);
        }

        if ($public = $result[0]['public'] || $show) {
            return $result[0]['name'].'='.$result[0]['value'];
        }

        return $result[0]['name'].'=[secure]';
    }
}

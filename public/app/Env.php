<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class Env extends DBModel
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
     * @return string
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
     * @return array|string
     *
     * @throws Exception
     */
    public static function get(int $id, int $rid, bool $show = false, $git_type = 'github')
    {
        $sql = 'SELECT name,value,public FROM env_vars WHERE id=? AND git_type=? AND rid=?';

        $output = DB::select($sql, [$id, $git_type, $rid]);

        if (!$output) {
            throw new Exception('Not Found', 404);
        }

        if ($public = $output[0]['public'] || $show) {
            return $output[0]['name'].'='.$output[0]['value'];
        }

        return $output[0]['name'].'=[secure]';
    }
}

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
    public static function list(string $git_type, int $rid)
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
    public static function create(string $git_type, int $rid, string $name, string $value, bool $public)
    {
        $sql = 'INSERT INTO env_vars VALUES(null,?,?,?,?,?)';

        return DB::insert($sql, [$git_type, $rid, $name, $value, (int) $public]);
    }

    /**
     * @param int    $id
     * @param string $git_type
     * @param int    $rid
     * @param string $name
     * @param string $value
     *
     * @return int
     *
     * @throws Exception
     */
    public static function update(int $id, string $git_type, int $rid, string $name, string $value)
    {
        $sql = 'UPDATE env_vars SET value=? WHERE id=? AND git_type=? AND rid=? AND name=?';

        return DB::update($sql, [$value, $id, $git_type, $rid, $name]);
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
    public static function delete(int $id, string $git_type, int $rid)
    {
        $sql = 'DELETE FROM env_vars WHERE id=? AND git_type=? AND rid=?';

        return DB::delete($sql, [$id, $git_type, $rid]);
    }
}

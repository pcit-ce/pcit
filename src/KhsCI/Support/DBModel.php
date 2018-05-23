<?php

declare(strict_types=1);
/** @noinspection SqlResolve */

namespace KhsCI\Support;

use Exception;

class DBModel
{
    protected static $table = null;

    protected static $set_array = [];

    /**
     * 取得所有数据.
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function all()
    {
        $table = static::getTableName();

        $sql = "SELECT * from $table";

        return DB::select($sql, []);
    }

    /**
     * 通过主键查找数据.
     *
     * @param int $table_primary_key_id
     *
     * @return int|array
     *
     * @throws Exception
     */
    public static function find($table_primary_key_id)
    {
        $table = static::getTableName();

        $sql = "SELECT * from $table WHERE id = ?";

        $output = DB::select($sql, [$table_primary_key_id]);

        if ($output) {
            return $output[0];
        }

        return false;
    }

    /**
     * 通过主键查找数据，找不到则抛出异常.
     *
     * @param int|array $table_primary_key_id
     *
     * @return array|int
     *
     * @throws Exception
     */
    public static function findOrFail($table_primary_key_id)
    {
        $output = static::find($table_primary_key_id);

        if ($output) {
            return $output;
        }

        throw new Exception(__FILE__.' : '.__LINE__.' Not Found', 404);
    }

    /**
     * @return null|string
     */
    public static function getTableName()
    {
        $table = static::$table;

        if (!$table) {
            $table = StringSupport::uncamelize(
                array_slice(explode('\\', static::class), -1, 1)[0].'s');
        }

        return $table;
    }

    /**
     * @throws Exception
     */
    public function save(): void
    {
        $table = self::getTableName();

        foreach (static::$set_array as $k => $v) {
            $sql = "INSERT INTO {$table}($k) VALUES(?)";

            DB::insert($sql, [$v]);
        }
    }

    public function __set($name, $value): void
    {
        $this->$name = $value;

        static::$set_array[$name] = $value;
    }

    /**
     * @return array|string
     *
     * @throws Exception
     */
    public static function getLastKeyId()
    {
        $table = self::getTableName();

        return DB::select("SELECT id FROM $table ORDER BY id DESC LIMIT 1", null, true);
    }
}

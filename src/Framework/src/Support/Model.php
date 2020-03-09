<?php

declare(strict_types=1);
/** @noinspection SqlResolve */

namespace PCIT\Framework\Support;

use Exception;

class Model
{
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected static $table = null;

    protected static $set_array = [];

    protected static $primaryKey = 'id';

    protected static $incrementing = true;

    /**
     * 如果主键不是一个整数，则应该在模型上设置 string.
     */
    protected static $keyType = '';

    /**
     * 默认数据表中存在 created_at 和 updated_at 这两个字段.
     */
    protected static $timestamps = true;

    protected static $connection = '';

    /**
     * 结果分块.
     */
    public static function chunk(int $count, \Closure $closer): void
    {
    }

    /**
     * 取得所有数据.
     *
     * @return array|string
     *
     * @throws \Exception
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
     * @throws \Exception
     */
    public static function find($table_primary_key_id)
    {
        $table = static::getTableName();

        $sql = "SELECT * from $table WHERE id=?";

        $result = DB::select($sql, [$table_primary_key_id]);

        if ($result) {
            return $result[0];
        }

        return false;
    }

    /**
     * 取回符合查询限制的第一个模型 ...
     */
    public static function first(): void
    {
    }

    /**
     * 通过主键查找数据，找不到则抛出异常.
     *
     * @param int|array $table_primary_key_id
     *
     * @return array|int
     *
     * @throws \Exception
     */
    public static function findOrFail($table_primary_key_id)
    {
        $result = static::find($table_primary_key_id);

        if ($result) {
            return $result;
        }

        throw new Exception(__FILE__.' : '.__LINE__.' Not Found', 404);
    }

    public static function firstOrFail(): void
    {
    }

    /**
     * @return string|null
     */
    public static function getTableName()
    {
        $table = static::$table;

        if (!$table) {
            $table = StringSupport::uncamelize(
                \array_slice(explode('\\', static::class), -1, 1)[0].'s');
        }

        return $table;
    }

    /**
     * @throws \Exception
     */
    public function save(): void
    {
        $table = self::getTableName();

        foreach (static::$set_array as $k => $v) {
            $sql = "INSERT INTO {$table}($k) VALUES(?)";

            DB::insert($sql, [$v]);
        }

        static::$set_array = [];
    }

    public function __set($name, $value): void
    {
        $this->$name = $value;

        static::$set_array[$name] = $value;
    }

    /**
     * @return array|string
     *
     * @throws \Exception
     */
    public static function getLastKeyId()
    {
        $table = self::getTableName();

        return DB::select("SELECT id FROM $table ORDER BY id DESC LIMIT 1", null, true);
    }

    /**
     * @param int|array $primaryKeyValue 通过主键删除数据
     */
    public static function destroy($primaryKeyValue): void
    {
    }

//    public static function delete(): void
//    {
//    }
}

<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Framework\Support\DB;
use PCIT\Framework\Support\Model;
use PCIT\Support\CI;

class Setting extends Model
{
    protected static $table = 'settings';

    protected static $setting_array = CI::CI_SETTING_ARRAY;

    /**
     * 返回某仓库的设置列表.
     *
     * @return array
     *
     * @throws Exception
     */
    public static function list(int $rid, string $git_type = 'github')
    {
        $sql = <<<'EOF'
SELECT
build_pushes,
build_pull_requests,
maximum_number_of_builds,
auto_cancel_branch_builds,
auto_cancel_pull_request_builds
FROM settings WHERE git_type=? AND rid=?
EOF;

        return DB::select($sql, [$git_type, $rid]);
    }

    /**
     * 返回某个设置的值
     *
     * @return string
     *
     * @throws Exception
     */
    public static function get(int $rid, string $setting_name, string $git_type = 'github')
    {
        if (!\in_array($setting_name, self::$setting_array, true)) {
            throw new Exception('Not Found', 404);
        }

        $sql = "SELECT $setting_name FROM settings WHERE git_type=? AND rid=? LIMIT 1";

        return DB::select($sql, [$git_type, $rid], true);
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public static function update(int $rid, string $setting_name, string $setting_value, string $git_type = 'github')
    {
        if (!\in_array($setting_name, self::$setting_array, true)) {
            throw new Exception('Not Found', 404);
        }

        $sql = "UPDATE settings SET $setting_name=? WHERE git_type=? AND rid=?";

        $result = DB::update($sql, [$setting_value, $git_type, $rid]);

        // 当用户更新设置时才新建列
        if (0 === $result) {
            $sql = <<<EOF
INSERT INTO settings(git_type, rid,$setting_name) values(?,?,?)
EOF;

            $result = DB::insert($sql, [$git_type, $rid, $setting_value]);
        }

        return $result;
    }
}

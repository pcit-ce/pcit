<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use Exception;
use PCIT\Support\DB;

class Migrate
{
    /**
     * @param $sql_file
     *
     * @throws Exception
     */
    public static function migrate(string $sql_file): void
    {
        if (\in_array($sql_file, self::getSqlFileList(), true)) {
            self::migrateSqlFile(base_path().'framework/sql/'.$sql_file);
        } else {
            var_dump(self::getSqlFileList());
        }

        return;
    }

    /**
     * @throws Exception
     */
    public static function all(): void
    {
        foreach (self::getSqlFileList() as $file) {
            echo "\n\n===> Migrate $file ...\n\n";

            self::migrateSqlFile(base_path().'framework/sql/'.$file);
        }

        return;
    }

    /**
     * exec sql file.
     *
     * @param string $file
     */
    private static function migrateSqlFile(string $file): void
    {
        $content = file_get_contents($file);

        foreach (explode(';', $content) as $k) {
            try {
                if (!$k) {
                    continue;
                }
                DB::statement($k);
            } catch (\Throwable $e) {
                // 数据库执行出错则退出
                echo 'DB_Error: '.$e->getMessage();
            }
        }
    }

    /**
     * get sql file list.
     *
     * @return array
     */
    private static function getSqlFileList()
    {
        $sqlFileList = scandir(base_path().'framework/sql');

        $sqlFileList = array_filter($sqlFileList, function ($k) {
            if (\in_array($k, ['.', '..'], true)) {
                return false;
            }

            $spl = new \SplFileInfo(base_path().'framework/sql/'.$k);
            $ext = $spl->getExtension();

            return 'sql' === $ext;
        });

        return $sqlFileList;
    }

    /**
     * @throws Exception
     */
    public static function cleanup()
    {
        return DB::statement('DROP DATABASE pcit; CREATE DATABASE pcit');
    }
}

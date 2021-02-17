<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use PCIT\Framework\Support\DB;

class Migrate
{
    /**
     * @param $sql_file
     */
    public static function migrate(string $sql_file): void
    {
        if (\in_array($sql_file, self::getSqlFileList(), true)) {
            self::migrateSqlFile(base_path('framework/sql/'.$sql_file));
        } else {
            var_dump(self::getSqlFileList());
        }
    }

    public static function all(): void
    {
        foreach (self::getSqlFileList() as $file) {
            echo "\n\n==> Migrate $file ...\n\n";

            self::migrateSqlFile(base_path('framework/sql/'.$file));
        }
    }

    /**
     * exec sql file.
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
                echo '[error]: '.$e->getMessage().\PHP_EOL;
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
        $sqlFileList = scandir(base_path('framework/sql'));

        return array_filter($sqlFileList, function ($k) {
            if (\in_array($k, ['.', '..'], true)) {
                return false;
            }

            $spl = new \SplFileInfo(base_path('framework/sql/'.$k));
            $ext = $spl->getExtension();

            return 'sql' === $ext;
        });
    }

    public static function cleanup()
    {
        return DB::statement('DROP DATABASE pcit; CREATE DATABASE pcit');
    }
}

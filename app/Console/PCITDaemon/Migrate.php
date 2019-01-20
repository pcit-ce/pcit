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
        if (\in_array($sql_file, self::getSqlList(), true)) {
            self::execFromFile(base_path().'framework/sql/'.$sql_file);
        } else {
            var_dump(self::getSqlList());
        }

        return;
    }

    /**
     * @throws Exception
     */
    public static function all(): void
    {
        foreach (self::getSqlList() as $file) {
            echo "\n\n===> Migrate $file ...\n\n";

            self::execFromFile(base_path().'framework/sql/'.$file);
        }

        return;
    }

    /**
     * exec sql file.
     *
     * @param string $file
     */
    private static function execFromFile(string $file): void
    {
        $content = file_get_contents($file);

        foreach (explode(';', $content) as $k) {
            try {
                if (!$k) {
                    continue;
                }
                DB::statement($k);
            } catch (\Throwable $e) {
                echo $e->getMessage().$e->getCode();
            }
        }
    }

    /**
     * get sql file list.
     *
     * @return array
     */
    private static function getSqlList()
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

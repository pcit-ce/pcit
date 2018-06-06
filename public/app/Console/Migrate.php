<?php

declare(strict_types=1);

namespace App\Console;

use Exception;
use KhsCI\Support\DB;

class Migrate
{
    /**
     * @param $sql_file
     *
     * @throws Exception
     */
    public static function migrate(string $sql_file): void
    {
        if (in_array($sql_file, self::getSqlList(), true)) {
            self::execFromFile(__DIR__.'/../../sql/'.$sql_file);
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
            echo "Migrate $file ...\n\n";

            self::execFromFile(__DIR__.'/../../sql/'.$file);
        }

        return;
    }

    private static function execFromFile(string $file): void
    {
        $content = file_get_contents($file);

        foreach (explode(';', $content) as $k) {
            try {
                DB::statement($k);
            } catch (\Throwable $e) {
            }
        }
    }

    private static function getSqlList()
    {
        $array = scandir(__DIR__.'/../../sql');

        $array = array_filter($array, function ($k) {
            if (in_array($k, ['.', '..'], true)) {
                return false;
            }

            $spl = new \SplFileInfo(__DIR__.'/../../sql/'.$k);

            $ext = $spl->getExtension();

            if ('sql' !== $ext) {
                return false;
            }

            return true;
        });

        return $array;
    }

    /**
     * @throws Exception
     */
    public static function cleanup()
    {
        return DB::statement('DROP DATABASE test; CREATE DATABASE test');
    }
}

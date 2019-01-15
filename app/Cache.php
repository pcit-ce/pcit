<?php

declare(strict_types=1);

namespace App;

use PCIT\Support\Model;

class Cache extends Model
{
    public static function insert(string $gitType,
                                  int $rid,
                                  string $branch,
                                  string $filename): void
    {
        $sql = 'INSERT INTO caches(git_type,rid,branch,filename,updated_at) values(?,?,?,?,?)';

        DB::insert($sql, [$gitType, $rid, $branch, $filename, time()]);
    }

    public static function update(string $gitType,
                                  int $rid,
                                  string $branch): void
    {
        $sql = 'UPDATE caches SET updated_at=? WHERE git_type=? AND rid=? AND branch=?';

        DB::update($sql, [time(), $gitType, $rid, $branch]);
    }
}

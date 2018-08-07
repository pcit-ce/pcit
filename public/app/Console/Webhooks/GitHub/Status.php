<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: SYSTEM
 * Date: 2018/8/6
 * Time: 19:38.
 */

namespace App\Console\Webhooks\GitHub;

use KhsCI\Support\DB;

class Status
{
    /**
     * @param $json_content
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function handle($json_content)
    {
        $sql = <<<'EOF'
        INSERT INTO builds(
        
        git_type,event_type
        
        ) VALUES(?,?);
EOF;

        return DB::insert($sql, [
                'github', __FUNCTION__,
            ]
        );
    }
}

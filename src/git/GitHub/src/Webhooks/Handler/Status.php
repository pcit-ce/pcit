<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\Framework\Support\DB;

class Status
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content)
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

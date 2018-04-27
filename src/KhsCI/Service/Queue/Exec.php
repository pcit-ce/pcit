<?php

declare(strict_types=1);

namespace KhsCI\Service\Queue;

use KhsCI\Support\CIConst;
use KhsCI\Support\DB;

class Exec
{
    public function exec(): void
    {
        $pdo = DB::connect();

        $build_status_pending = CIConst::BUILD_STATUS_PENDING;

        $sql = <<<EOF
SELECT git_type,rid,commit_id FROM builds WHERE build_activate=1 AND build_status='$build_status_pending';
EOF;

    }
}

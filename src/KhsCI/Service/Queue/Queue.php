<?php

declare(strict_types=1);

namespace KhsCI\Service\Queue;

use KhsCI\Support\CIConst;
use KhsCI\Support\DB;

class Queue
{
    private static $gitType;

    public function __invoke(): void
    {
        $pdo = DB::connect();

        $build_status_pending = CIConst::BUILD_STATUS_PENDING;

        $sql = <<<EOF
SELECT git_type,rid,commit_id,commit_message FROM builds WHERE build_status='$build_status_pending';
EOF;

        $output = $pdo->query($sql);

        foreach ($output as $k) {

            $git_type = $k[0];
            $rid = $k[1];
            self::$gitType = $git_type;
            $commit_id = $k[2];
            $commit_message = $k[3];

            // commit 信息跳过构建
            $skip = self::skip($commit_message);

            if ($skip) {
                $build_status_skip = CIConst::BUILD_STATUS_SKIP;
                $sql = "UPDATE builds SET build_status=? WHERE git_type=? AND commit_id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$build_status_skip, self::$gitType, $commit_id]);

                continue;
            }

            // 是否启用构建
            $build_activated = self::getRepoBuildActivateStatus($rid);

            if ($build_activated) {
                self::run($rid, $commit_id);
            } else {
                self::inactive($rid);
            }
        }
    }

    /**
     * 检查是否启用了构建
     *
     * @param $rid
     * @return bool
     */
    private function getRepoBuildActivateStatus($rid)
    {
//        $redis = Cache::connect();
//
//        $redis->hExists(1, 2);

        $gitType = self::$gitType;

        $pdo = DB::connect();

        $sql = <<<EOF
SELECT build_activate FROM repo WHERE rid=$rid AND git_type='$gitType';
EOF;

        $output = $pdo->query($sql);

        foreach ($output as $k) {
            if (0 == $k[0]) {
                return false;
            }
        }

        return true;
    }

    /**
     * 执行构建
     * @param $rid
     * @param $commit_id
     */
    private function run($rid, $commit_id)
    {
        echo "running....";
        CIConst::BUILD_STATUS_ERRORED;
        CIConst::BUILD_STATUS_FAILED;
        CIConst::BUILD_STATUS_PASSED;
    }

    /**
     * 检查 commit 信息跳过构建
     *
     * @param $commit_message
     * @return bool
     */
    private function skip(string $commit_message)
    {
        $output = stripos($commit_message, '[skip ci]');
        $output2 = stripos($commit_message, '[ci skip]');

        if ($output === false && $output2 === false) {
            return false;
        }

        return true;

    }

    /**
     * @param $rid
     * @param int $lastId
     */
    private function inactive($rid, int $lastId = 0)
    {
        $pdo = DB::connect();

        $gitType = self::$gitType;

        $build_status_inactive = CIConst::BUILD_STATUS_INACTIVE;

        $sql = <<<EOF
UPDATE builds set build_status='$build_status_inactive' WHERE git_type='$gitType' AND rid='$rid' AND id>$lastId;
EOF;
        $pdo->exec($sql);
    }
}

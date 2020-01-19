<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Framework\Support\DB;
use PCIT\Framework\Support\Model;
use PCIT\Support\CI;

class Job extends Model
{
    public static $table = 'jobs';

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function getLog(int $job_id)
    {
        $sql = 'SELECT build_log FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }

    public static function deleteLog(int $job_id): void
    {
        DB::update('UPDATE jobs SET build_log=null WHERE id=?', [$job_id]);
    }

    /**
     * @throws Exception
     */
    public static function updateLog(int $job_id, string $build_log): void
    {
        $sql = 'UPDATE jobs SET build_log=? WHERE id=?';

        DB::update($sql, [$build_log, $job_id]);
    }

    /**
     * @throws Exception
     */
    public static function create(int $build_id): int
    {
        $sql = <<<'EOF'
INSERT INTO jobs(id,allow_failure,state,created_at,build_id)

values(null,?,?,?,?)
EOF;

        return DB::insert($sql, [0, 'queued', time(), $build_id]);
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public static function getByBuildKeyID(int $build_key_id, bool $queued = false)
    {
        if ($queued) {
            $sql = 'SELECT id FROM jobs WHERE build_id=? AND state=?';

            return DB::select($sql, [$build_key_id, 'queued']);
        }

        $sql = 'SELECT id FROM jobs WHERE build_id=?';

        return DB::select($sql, [$build_key_id]);
    }

    /**
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByBuildKeyID(int $build_key_id)
    {
        $sql = 'SELECT * FROM jobs WHERE build_id=?';

        $jobs = DB::select($sql, [$build_key_id]);

        for ($i = 0; $i < \count($jobs); ++$i) {
            $job_id = $jobs[$i]['id'];

            // 获取状态
            $state = $jobs[$i]['state'];

            if (\in_array($state, [CI::GITHUB_CHECK_SUITE_STATUS_QUEUED])) {
                $jobs[$i]['build_log'] = '{"running":"running"}';
            }
        }

        return $jobs;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public static function getJobIDByBuildKeyID(int $build_key_id)
    {
        $sql = 'SELECT id FROM jobs WHERE build_id=?';

        $result = DB::select($sql, [$build_key_id]);

        $array = [];

        foreach ($result as $key => $value) {
            foreach ($value as $k => $v) {
                $array[] = $v;
            }
        }

        return $array;
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public static function getRid(int $job_id)
    {
        $sql = 'SELECT builds.rid FROM jobs RIGHT JOIN builds ON jobs.build_id=builds.id WHERE jobs.id=? LIMIT 1';

        $rid = DB::select($sql, [$job_id], true);

        if ($rid) {
            return (int) $rid;
        }

        self::updateBuildStatus($job_id, 'errored');

        throw new Exception('', 404);
    }

    /**
     * 事件创建时间.
     *
     * @param int $time
     *
     * @throws Exception
     */
    public static function updateCreatedAt(int $job_id, ?int $time): void
    {
        $sql = 'UPDATE jobs SET created_at=? WHERE id=?';

        DB::update($sql, [$time, $job_id]);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function getCreatedAt(int $job_id)
    {
        $sql = 'SELECT created_at FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }

    /**
     * 容器运行开始时间.
     *
     * @param int $time
     *
     * @throws Exception
     */
    public static function updateStartAt(int $job_id, ?int $time): void
    {
        $sql = 'UPDATE jobs SET started_at=? WHERE id=?';

        DB::update($sql, [$time, $job_id]);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function getStartAt(int $job_id)
    {
        $sql = 'SELECT started_at FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }

    /**
     * @throws Exception
     */
    public static function updateFinishedAt(int $job_id, ?int $time): void
    {
        $sql = 'UPDATE jobs SET finished_at=? WHERE id=?';

        DB::update($sql, [$time, $job_id]);
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public static function getFinishedAt(int $job_id)
    {
        $sql = 'SELECT finished_at FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }

    /**
     * @return array|string|null
     *
     * @throws Exception
     */
    public static function getFinishedAtByBuildId(int $build_id)
    {
        $sql = 'SELECT state FROM jobs WHERE build_id=? GROUP BY state';

        $state = DB::select($sql, [$build_id]);

        foreach ($state as $k => $v) {
            $state = $v['state'];

            if (\in_array($state, ['queued', 'pending', 'in_progress'])) {
                return null;
            }
        }

        $sql = 'SELECT max(finished_at) FROM jobs WHERE build_id=?';

        return DB::select($sql, [$build_id], true);
    }

    /**
     * @param string $status
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateBuildStatus(int $job_key_id, ?string $status)
    {
        $sql = 'UPDATE jobs SET state=? WHERE id=?';

        return DB::update($sql, [$status, $job_key_id]);
    }

    /**
     * @return array|string
     *
     * @throws Exception
     */
    public static function getBuildStatus(int $job_key_id)
    {
        $sql = 'SELECT state FROM jobs WHERE id=?';

        return DB::select($sql, [$job_key_id]);
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public static function getCheckRunId(int $build_key_id)
    {
        $sql = 'SELECT check_run_id FROM jobs WHERE id=? LIMIT 1';

        $result = DB::select($sql, [$build_key_id], true);

        return $result;
    }

    /**
     * @param int $check_run_id
     *
     * @throws Exception
     */
    public static function updateCheckRunId(?int $check_run_id, int $build_key_id): void
    {
        $sql = 'UPDATE jobs SET check_run_id=? WHERE id=?';

        DB::update($sql, [$check_run_id, $build_key_id]);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function getGitType(int $job_key_id)
    {
        $sql = 'SELECT builds.git_type FROM jobs JOIN builds ON jobs.build_id = builds.id WHERE jobs.id = ? LIMIT 1';

        return DB::select($sql, [$job_key_id], true);
    }

    /**
     * @return int
     *
     * @throws Exception
     */
    public static function getBuildKeyId(int $job_key_id)
    {
        $sql = 'SELECT build_id FROM jobs WHERE id =?';

        return (int) DB::select($sql, [$job_key_id], true);
    }

    /**
     * 从 build 的所有 job 得出 build 的状态
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getBuildStatusByBuildKeyId(int $build_key_id)
    {
        $status = DB::select(
            'SELECT state FROM jobs WHERE build_id=? GROUP BY state', [$build_key_id]);

        if (1 === \count($status)) {
            $state = $status[0]['state'];

            $state = 'pending' === $state ? 'queued' : $state;

            return $state;
        }

        // 有一个 error failure 均返回对应值
        foreach ($status as $state) {
            if ('cancelled' === $state['state']) {
                return 'cancelled';
            }

            if ('error' === $state['state']) {
                return 'error';
            }

            if ('failure' === $state['state']) {
                return 'failure';
            }

            if ('skip' === $state['state']) {
                return 'skip';
            }
        }

        return 'queued';
    }

    /**
     * @throws Exception
     */
    public static function updateEnv(int $job_id, string $env): void
    {
        DB::update('UPDATE jobs set env_vars=? WHERE id=?', [$env, $job_id]);
    }

    /**
     * @throws Exception
     */
    public static function getEnv(int $job_id): ?array
    {
        $result = DB::select('SELECT env_vars FROM jobs WHERE id=?', [$job_id], true);

        if (null === $result) {
            return null;
        }

        return json_decode($result, true);
    }

    /**
     * @throws Exception
     */
    public static function getJobIDByBuildKeyIDAndEnv(int $buildId, string $env): int
    {
        $sql = 'SELECT id FROM jobs WHERE build_id=? AND env_vars=?';

        return (int) DB::select($sql, [$buildId, $env], true);
    }

    /**
     * @return array|string
     *
     * @throws Exception
     */
    public static function getQueuedJob()
    {
        return DB::select('SELECT * FROM jobs WHERE state=? ORDER BY id LIMIT 1', ['queued']);
    }
}

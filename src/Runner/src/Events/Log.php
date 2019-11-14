<?php

declare(strict_types=1);

namespace PCIT\Builder\Events;

use Exception;
use PCIT\Framework\Support\Cache;
use PCIT\Framework\Support\Date;
use PCIT\Framework\Support\Log as LogSupport;
use PCIT\PCIT;
use PCIT\Support\CacheKey;
use PCIT\Support\CI;

class Log
{
    private $container_id;

    private $job_id;

    private $pipeline;

    private $cache;

    public function __construct(int $job_id, string $container_id, string $pipeline = null)
    {
        $this->job_id = $job_id;
        $this->container_id = $container_id;
        $this->pipeline = $pipeline;
        $this->cache = Cache::store();
    }

    /**
     * @param $job_id
     *
     * @throws Exception
     */
    public static function drop(int $job_id): void
    {
        LogSupport::debug(__FILE__, __LINE__, 'Drop prev logs '.$job_id, [], LogSupport::EMERGENCY);

        Cache::store()->del(CacheKey::logHashKey($job_id));
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function handle()
    {
        $cache = $this->cache;

        $i = -1;

        $startedAt = null;
        $finishedAt = null;
        $until_time = 0;

        $docker_container = app(PCIT::class)->docker->container;

        while (1) {
            // 循环遍历日志
            $i = $i + 1;

            $image_status_obj = json_decode($docker_container->inspect($this->container_id))->State;
            $status = $image_status_obj->Status;
            $startedAt = Date::parse($image_status_obj->StartedAt);

            if ('running' === $status) {
                // 处于运行状态
                if (0 === $i) {
                    $since_time = $startedAt;
                    $until_time = $startedAt;
                } else {
                    $since_time = $until_time;
                    $until_time = $until_time + 1;
                }

                $image_log = $docker_container->logs(
                    $this->container_id, false, true, true,
                    $since_time, $until_time, true
                );

                // echo $image_log;

                sleep(2);

                continue;
            } else {
                // 容器停止，获取日志
                $image_log = $docker_container->logs(
                    $this->container_id, false, true, true, 0, 0, true
                );

                if (!$image_log) {
                    $image_log = '12345678 log not found!';
                }

                $cache->hset(
                    CacheKey::logHashKey($this->job_id), $this->pipeline, $image_log);

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $image_status_obj->StartedAt;
                $finishedAt = $image_status_obj->FinishedAt;

                $exitCode = $image_status_obj->ExitCode;

                if (0 !== $exitCode) {
                    LogSupport::debug(__FILE__, __LINE__, "Container $this->container_id ExitCode is $exitCode, not 0", [], LogSupport::ERROR);

                    throw new Exception(CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
                }

                break;
            }
        }

        return [
            'start' => $startedAt,
            'stop' => $finishedAt,
        ];
    }
}

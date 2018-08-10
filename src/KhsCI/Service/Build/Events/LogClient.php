<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Events;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\Log;

class LogClient
{
    private $container_id;

    private $job_id;

    public function __construct(int $job_id, string $container_id)
    {
        $this->job_id = $job_id;
        $this->container_id = $container_id;
    }

    /**
     * @param $job_id
     *
     * @throws Exception
     */
    public static function drop($job_id): void
    {
        Log::debug(__FILE__, __LINE__, 'Drop prev logs '.$job_id, [], Log::EMERGENCY);

        Cache::store()->hDel('build_log', $job_id);
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function handle()
    {
        $redis = Cache::store();

        $i = -1;

        $startedAt = null;
        $finishedAt = null;
        $until_time = 0;

        $docker_container = (new KhsCI())->docker->container;

        while (1) {
            $i = $i + 1;

            $image_status_obj = json_decode($docker_container->inspect($this->container_id))->State;
            $status = $image_status_obj->Status;
            $startedAt = Date::parse($image_status_obj->StartedAt);

            if ('running' === $status) {
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

                echo $image_log;

                sleep(1);

                continue;
            } else {
                $image_log = $docker_container->logs(
                    $this->container_id, false, true, true, 0, 0, true
                );

                $prev_docker_log = $redis->hget('build_log', (string) $this->job_id);

                $redis->hset(
                    'build_log',
                    (string) $this->job_id, $prev_docker_log.PHP_EOL.PHP_EOL.$image_log
                );

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $image_status_obj->StartedAt;
                $finishedAt = $image_status_obj->FinishedAt;

                $exitCode = $image_status_obj->ExitCode;

                if (0 !== $exitCode) {
                    Log::debug(__FILE__, __LINE__, "Container $this->container_id ExitCode is $exitCode, not 0", [], Log::ERROR);

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

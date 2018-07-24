<?php

namespace KhsCI\Service\Build;

use Docker\Container\Client as Container;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Date;
use KhsCI\Support\Log;

class LogClient
{
    /**
     * @param int       $build_key_id
     * @param Container $docker_container
     * @param string    $container_id
     *
     * @return array
     *
     * @throws Exception
     */
    public function docker_container_logs(int $build_key_id, Container $docker_container, string $container_id)
    {
        $redis = Cache::connect();

        if ('/bin/drone-git' === json_decode($docker_container->inspect($container_id))->Path) {
            Log::debug(__FILE__,
                __LINE__,
                'Drop prev logs '.$build_key_id,
                [],
                Log::EMERGENCY
            );

            $redis->hDel('build_log', $build_key_id);
        }

        $i = -1;

        $startedAt = null;
        $finishedAt = null;
        $until_time = 0;

        while (1) {
            $i = $i + 1;

            $image_status_obj = json_decode($docker_container->inspect($container_id))->State;
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
                    $container_id, false, true, true,
                    $since_time, $until_time, true
                );

                echo $image_log;

                sleep(1);

                continue;
            } else {
                $image_log = $docker_container->logs(
                    $container_id, false, true, true, 0, 0, true
                );

                $prev_docker_log = $redis->hget('build_log', (string) $build_key_id);

                $redis->hset(
                    'build_log',
                    (string) $build_key_id, $prev_docker_log.PHP_EOL.PHP_EOL.$image_log
                );

                /**
                 * 2018-05-01T05:16:37.6722812Z
                 * 0001-01-01T00:00:00Z.
                 */
                $startedAt = $image_status_obj->StartedAt;
                $finishedAt = $image_status_obj->FinishedAt;

                $exitCode = $image_status_obj->ExitCode;

                if (0 !== $exitCode) {
                    Log::debug(__FILE__, __LINE__, "Container $container_id ExitCode is $exitCode, not 0", [], Log::ERROR);

                    throw new Exception(CI::BUILD_STATUS_ERRORED);
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
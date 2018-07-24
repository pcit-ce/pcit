<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Docker\Container\Client as Container;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\Log;

class PipelineClient
{
    /**
     * @param array     $pipeline
     * @param array     $config
     * @param string    $event_type
     * @param array     $system_env
     * @param string    $work_dir
     * @param Container $docker_container
     * @param int       $job_id
     *
     * @throws Exception
     */
    public static function config(array $pipeline,
                                  ?array $config,
                                  string $event_type,
                                  array $system_env,
                                  string $work_dir,
                                  Container $docker_container,
                                  int $job_id): void
    {
        foreach ($pipeline as $setup => $array) {
            Log::debug(__FILE__, __LINE__, 'This Pipeline is '.$setup, [], Log::EMERGENCY);

            $image = $array['image'];
            $commands = $array['commands'] ?? null;
            $event = $array['when']['event'] ?? null;
            $env = $array['environment'] ?? [];
            $status = $array['when']['status'] ?? null;
            $shell = $array['shell'] ?? 'sh';

            if (!self::parseEvent($event, $event_type)) {
                continue;
            }

            $no_status = false;
            $failure = self::parseStatus($status, 'failure');
            $success = self::parseStatus($status, 'success');
            $changed = self::parseStatus($status, 'changed');

            if (!$status) {
                $no_status = true;
            }

            $image = ParseClient::image($image, $config);
            $ci_script = ParseClient::command($setup, $image, $commands);

            $env = array_merge(["CI_SCRIPT=$ci_script"], $env, $system_env);

            Log::debug(__FILE__, __LINE__, json_encode($env), [], Log::INFO);

            $shell = '/bin/'.$shell;

            $cmd = ['echo $CI_SCRIPT | base64 -d | '.$shell.' -e'];

            $container_config = $docker_container
                ->setEnv($env)
                ->setBinds(["$job_id:$work_dir", 'tmp:/tmp'])
                ->setEntrypoint(["$shell", '-c'])
                ->setLabels([
                    'com.khs1994.ci.pipeline' => $job_id,
                    'com.khs1994.ci.pipeline.name' => $setup,
                    'com.khs1994.ci.pipeline.status.no_status' => $no_status,
                    'com.khs1994.ci.pipeline.status.failure' => $failure,
                    'com.khs1994.ci.pipeline.status.success' => $success,
                    'com.khs1994.ci.pipeline.status.changed' => $changed,
                    'com.khs1994.ci' => $job_id
                ])
                ->setWorkingDir($work_dir)
                ->setCmd($cmd)
                ->setImage($image)
                ->setNetworkingConfig([
                    'EndpointsConfig' => [
                        "$job_id" => [
                            'Aliases' => [
                                $setup
                            ]
                        ]
                    ]
                ])
                ->setCreateJson()
                ->getCreateJson();

            Cache::connect()->lPush((string) $job_id, $container_config);
        }
    }

    /**
     * @param        $event
     * @param string $event_type
     *
     * @return bool
     * @throws Exception
     */
    private static function parseEvent($event, string $event_type)
    {
        if ($event) {
            if (is_string($event)) {
                if ($event_type !== $event) {
                    Log::debug(
                        __FILE__,
                        __LINE__,
                        "Pipeline $event Is Not Current ".$event_type.'. Skip', [], Log::EMERGENCY
                    );

                    return false;
                }
            } elseif (is_array($event) and (!in_array($event_type, $event, true))) {
                Log::debug(
                    __FILE__,
                    __LINE__,
                    "Pipeline Event $event not in ".implode(' | ', $event).'. skip', [], Log::EMERGENCY);

                return false;
            }

            return true;
        }

        return true;
    }

    /**
     * @param $status
     * @param $target
     *
     * @return bool
     */
    private static function parseStatus($status, $target)
    {
        if (!$status) {
            return false;
        }

        if (is_string($status)) {
            if (in_array($status, ['failure', 'success', 'changed']))
                return $status === $target;
        }

        if (is_array($status)) {
            foreach ($status as $k) {
                if ($k === $target) {
                    return true;
                }
            }
        }

        return false;
    }
}

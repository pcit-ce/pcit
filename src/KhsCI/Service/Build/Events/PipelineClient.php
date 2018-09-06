<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Events;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\Build\BuildData;
use KhsCI\Service\Build\Client;
use KhsCI\Service\Build\ParseClient;
use KhsCI\Support\Cache;
use KhsCI\Support\Log;

class PipelineClient
{
    private $pipeline;
    private $matrix_config;
    private $build;
    private $client;

    public function __construct($pipeline, BuildData $build, Client $client, ?array $matrix_config)
    {
        $this->pipeline = $pipeline;
        $this->matrix_config = $matrix_config;
        $this->build = $build;
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $docker_container = (new KhsCI())->docker->container;

        $job_id = $this->client->job_id;

        $workdir = $this->client->workdir;

        foreach ($this->pipeline as $setup => $array) {
            Log::debug(__FILE__, __LINE__, 'This Pipeline is '.$setup, [], Log::EMERGENCY);

            $image = $array->image;
            $commands = $array->commands ?? null;
            $event = $array->when->event ?? null;
            $env = $array->environment ?? [];
            $status = $array->when->status ?? null;
            $shell = $array->shell ?? 'sh';

            if (!self::parseEvent($event, $this->build->event_type)) {
                continue;
            }

            $no_status = false;
            $failure = self::parseStatus($status, 'failure');
            $success = self::parseStatus($status, 'success');
            $changed = self::parseStatus($status, 'changed');

            if (!$status) {
                $no_status = true;
            }

            $image = ParseClient::image($image, $this->matrix_config);
            $ci_script = ParseClient::command($setup, $image, $commands);

            $env = array_merge(["CI_SCRIPT=$ci_script"], $env, $this->client->system_env);

            Log::debug(__FILE__, __LINE__, json_encode($env), [], Log::INFO);

            $shell = '/bin/'.$shell;

            $cmd = ['echo $CI_SCRIPT | base64 -d | '.$shell.' -e'];

            $container_config = $docker_container
                ->setEnv($env)
                ->setBinds(["$job_id:$workdir", 'tmp:/tmp'])
                ->setEntrypoint(["$shell", '-c'])
                ->setLabels([
                    'com.khs1994.ci.pipeline' => $job_id,
                    'com.khs1994.ci.pipeline.name' => $setup,
                    'com.khs1994.ci.pipeline.status.no_status' => $no_status,
                    'com.khs1994.ci.pipeline.status.failure' => $failure,
                    'com.khs1994.ci.pipeline.status.success' => $success,
                    'com.khs1994.ci.pipeline.status.changed' => $changed,
                    'com.khs1994.ci' => $job_id,
                ])
                ->setWorkingDir($workdir)
                ->setCmd($cmd)
                ->setImage($image)
                ->setNetworkingConfig([
                    'EndpointsConfig' => [
                        "$job_id" => [
                            'Aliases' => [
                                $setup,
                            ],
                        ],
                    ],
                ])
                ->setCreateJson(null)
                ->getCreateJson();

            Cache::store()->lPush((string) $job_id.'_pipeline', $container_config);
        }
    }

    /**
     * @param        $event
     * @param string $event_type
     *
     * @return bool
     *
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
            if (in_array($status, ['failure', 'success', 'changed'])) {
                return $status === $target;
            }
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

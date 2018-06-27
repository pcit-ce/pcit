<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use Docker\Container\Container;
use Docker\Image\Image;
use Exception;
use KhsCI\Support\Log;

class PipelineClient
{
    /**
     * @param array     $pipeline
     * @param array     $config
     * @param string    $event_type
     * @param array     $system_env
     * @param string    $work_dir
     * @param string    $unique_id
     * @param Container $docker_container
     * @param Image     $docker_image
     * @param int       $build_key_id
     *
     * @throws Exception
     */
    public static function runPipeline(array $pipeline,
                                       ?array $config,
                                       string $event_type,
                                       array $system_env,
                                       string $work_dir,
                                       string $unique_id,
                                       Container $docker_container,
                                       Image $docker_image,
                                       int $build_key_id): void
    {
        $client = new Client();

        foreach ($pipeline as $setup => $array) {
            Log::debug(__FILE__, __LINE__, 'This Pipeline is '.$setup, [], Log::EMERGENCY);

            $image = $array['image'];
            $commands = $array['commands'] ?? null;
            $event = $array['when']['event'] ?? null;
            $env = $array['environment'] ?? [];
            $status = $array['when']['status'] ?? null;
            $shell = $array['shell'] ?? 'sh';

            if ($event) {
                if (is_string($event)) {
                    if ($event_type !== $event) {
                        Log::debug(
                            __FILE__,
                            __LINE__,
                            "Pipeline $event Is Not Current ".$event_type.'. Skip', [], Log::EMERGENCY
                        );

                        continue;
                    }
                } elseif (is_array($event) and (!in_array($event_type, $event, true))) {
                    Log::debug(
                        __FILE__,
                        __LINE__,
                        "Pipeline Event $event not in ".implode(' | ', $event).'. skip', [], Log::EMERGENCY);

                    continue;
                }
            }

            if ($status) {
                continue;
            }

            if ('ci_docker_build' === $image) {
                continue;
            }

            $image = $client->parseImage($image, $config);

            $ci_script = $client->parseCommand($setup, $image, $commands);

            $env = array_merge(["CI_SCRIPT=$ci_script"], $env, $system_env);

            Log::debug(__FILE__, __LINE__, json_encode($env), [], Log::INFO);

            $shell = '/bin/'.$shell;

            $docker_container
                ->setEnv($env)
                ->setHostConfig(["$unique_id:$work_dir", 'tmp:/tmp'], $unique_id)
                ->setEntrypoint(["$shell", '-c'])
                ->setLabels(['com.khs1994.ci.pipeline' => $unique_id])
                ->setWorkingDir($work_dir);

            $cmd = ['echo $CI_SCRIPT | base64 -d | '.$shell.' -e'];

            // docker.khs1994.com:1000/username/image:1.14.0

            $image_array = explode(':', $image);

            // image not include :

            $tag = null;

            if (1 !== count($image_array)) {
                $tag = $image_array[count($image_array) - 1];
            }

            $docker_image->pull($image, $tag ?? 'latest');

            $container_id = $docker_container->start($docker_container->create($image, null, $cmd));

            Log::debug(
                __FILE__,
                __LINE__,
                'Run Container By Image '.$image.', Container Id is '.$container_id,
                [],
                Log::EMERGENCY
            );

            $client->docker_container_logs($build_key_id, $docker_container, $container_id);
        }
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker;

use Docker\Container\Client as Container;
use Exception;
use PCIT\PCIT;

class Cleanup
{
    /**
     * Remove all Docker Resource.
     *
     * @param string $id           services => only cleanup services
     * @param bool   $service_only only cleanup service container
     *
     * @throws Exception
     */
    public static function systemDelete(?string $id, bool $last = false, bool $service_only = false): void
    {
        if (null === $id and !$service_only) {
            return;
        }

        $label = 'com.khs1994.ci.service='.$id;

        $docker = app(PCIT::class)->docker;

        $docker_container = $docker->container;

        // $docker_image = $docker->image;

        // clean container

        self::deleteContainerByLabel($docker_container, $label);

        if ($service_only) {
            // 只清理服务，退出

            return;
        }

        // don't clean image

        // 全部构建任务结束之后才删除 volume、网络

        if ($last) {
            $docker_network = $docker->network;

            $docker_volume = $docker->volume;

            // clean all container

            self::deleteContainerByLabel($docker_container, 'com.khs1994.ci');

            // clean volume

            $docker_volume->remove('pcit_'.$id);
            $docker_volume->remove('pcit_actions_'.$id);

            \Log::emergency('Build Stopped Delete Volume '.$id);

            // clean network

            $docker_network->remove('pcit_'.$id);

            \Log::emergency('Build Stopped Delete Network '.$id);
        }
    }

    /**
     * @throws Exception
     */
    private static function deleteContainerByLabel(Container $container, string $label): void
    {
        $output = $container->list(true, null, false, ['label' => $label]);

        foreach (json_decode($output) as $k) {
            $id = $k->Id;

            if (!$id) {
                continue;
            }

            \Log::emergency('Delete Container '.$id);

            $container->remove($id, true, true);
        }
    }
}

<?php

namespace KhsCI\Service\Build;

use Exception;
use KhsCI\KhsCI;
use Docker\Container\Client as Container;
use KhsCI\Support\Log;

class Cleanup
{
    /**
     * Remove all Docker Resource.
     *
     * @param string $id services => only cleanup services
     * @param bool   $last
     * @param bool   $service_only
     *
     * @throws Exception
     */
    public static function systemDelete(?string $id, bool $last = false, bool $service_only = false): void
    {
        if (null === $id and !$service_only) {
            return;
        }

        $label = 'com.khs1994.ci.service='.$id;

        $docker = (new KhsCI())->docker;

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

            $docker_volume->remove($id);

            Log::connect()->emergency('Build Stoped Delete Volume '.$id);

            // clean network

            $docker_network->remove($id);

            Log::connect()->emergency('Build Stoped Delete Network '.$id);
        }
    }

    /**
     * @param Container $container
     * @param string    $label
     *
     * @throws Exception
     */
    public static function deleteContainerByLabel(Container $container, string $label): void
    {
        $output = $container->list(true, null, false, [
            'label' => $label,
        ]);

        foreach (json_decode($output) as $k) {
            $id = $k->Id;

            if (!$id) {
                continue;
            }

            Log::connect()->emergency('Delete Container '.$id);

            $container->remove($id, true, true);
        }
    }
}

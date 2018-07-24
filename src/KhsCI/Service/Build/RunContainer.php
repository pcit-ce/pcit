<?php

namespace KhsCI\Service\Build;

use KhsCI\CIException;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class RunContainer
{
    /**
     * @param int $build_key_id
     *
     * @throws CIException
     * @throws \Exception
     */
    public static function run(int $build_key_id)
    {
        $container_config = Cache::connect()->rPop((string) $build_key_id);

        $services_container_config = Cache::connect()->rPop((string) $build_key_id.'_services');

        Log::debug(__FILE__, __LINE__, 'Create Volume '.$build_key_id, [], Log::EMERGENCY);
        Log::debug(__FILE__, __LINE__, 'Create Network '.$build_key_id, [], Log::EMERGENCY);

        Log::debug(__FILE__, __LINE__, 'pull image plugins/git', [], Log::EMERGENCY);

        Log::debug(__FILE__, __LINE__, $image_pull_output, [], Log::EMERGENCY);

        try {
        } catch (\Throwable $e) {

            Log::debug(__FILE__, __LINE__, $e->__toString(), [], LOG::EMERGENCY);

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_PASSED:
                    $this->runSuccess();
                    break;
                default:
                    $this->runFailure();
            }

            throw new CIException($e->getMessage(), $this->build_key_id);
        }
    }

    public function pull(string $image)
    {
        // docker.khs1994.com:1000/username/image:1.14.0

        $image_array = explode(':', $image);

        // image not include :

        $tag = null;

        if (1 !== count($image_array)) {
            $tag = $image_array[count($image_array) - 1];
        }

        $docker_image->pull($image, $tag ?? 'latest');
    }
}
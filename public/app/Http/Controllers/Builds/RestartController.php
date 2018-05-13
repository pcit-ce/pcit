<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use Exception;
use KhsCI\Support\CI;

/**
 * Class RestartController.
 *
 * 用户点击重新构建按钮
 */
class RestartController
{
    /**
     * @param $build_key_id
     *
     * @throws Exception
     */
    public function __invoke($build_key_id): void
    {
        Build::UpdateBuildStatus($build_key_id, CI::BUILD_STATUS_PENDING);
    }
}

<?php

declare(strict_types=1);

namespace App\Console\KhsCIDaemon;

use App\Console\Events\Notification;
use KhsCI\KhsCI;
use KhsCI\Service\Build\Agent\RunContainer;
use KhsCI\Support\Log;

class Agent
{
    /**
     * @param int $build_key_id
     *
     * @throws \Exception
     */
    public function handle(int $build_key_id): void
    {
        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        (new KhsCI())->docker->system->ping(1);

        Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        try {
            (new RunContainer())->handle($build_key_id);
        } catch (\Throwable $e) {
            // 发送通知
            (new Notification($build_key_id, $e));
        }
    }
}

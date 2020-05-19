<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use App\Repo;

class DisableHandler
{
    public static function handle($repo_full_name, $git_type): void
    {
        if ('github' === $git_type) {
            return;
        }

        if (Repo::isActived($repo_full_name, $git_type)) {
            return;
        }

        throw new \Exception('🛑This repo not active');
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Framework\Dotenv;

class Dotenv
{
    public static function load(?string $app_env)
    {
        $env_file = $app_env ? '.env.'.$app_env : '.env';

        $env_file = file_exists(base_path().$env_file) ? $env_file : '.env';

        $env_file = file_exists(base_path().$env_file) ? $env_file : null;

        if ('testing' === $app_env and '.env' === $env_file) {
            // testing don't load .env, onlyload .env.testing
            $env_file = null;
        }

        $env_file && \Dotenv\Dotenv::createImmutable(base_path(), $env_file)->load();

        return $env_file;
    }
}

<?php

declare(strict_types=1);

namespace PCIT\Framework\Dotenv;

/**
 * @see https://github.com/vlucas/phpdotenv
 */
class Dotenv
{
    public static function load(?string $app_env)
    {
        $env_file = $app_env ? '.env.'.$app_env : '.env';

        $env_file = file_exists(base_path($env_file)) ? $env_file : '.env';

        $env_file = file_exists(base_path($env_file)) ? $env_file : null;

        if ('testing' === $app_env and '.env' === $env_file) {
            // testing don't load .env, onlyload .env.testing
            $env_file = null;
        }

        if ($env_file) {
            $dotenv = \Dotenv\Dotenv::createUnsafeImmutable(base_path(), $env_file)->load();

            // $dotenv ->required('CI_HOST');
        }

        return $env_file;
    }
}

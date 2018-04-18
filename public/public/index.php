<?php

declare(strict_types=1);

require_once __DIR__.'/../../vendor/autoload.php';

function open_error(): void
{
    ini_set('display_errors', 'on');
    ini_set('error_reporting', '32767');
}

/*
 * read .env.* file.
 */
try {
    $env = new Dotenv\Dotenv(__DIR__.'/../', '.env'.'.'.getenv('APP_ENV'));
    $env->load();
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

/*
 * Open Debug?
 */
$debug = getenv('CI_DEBUG') ?? false;

'true' === $debug && open_error();

/*
 *  SPL Autoload
 */
spl_autoload_register(function ($class): void {
    $class = str_replace('App\\Http', 'app\\Http', $class);
    $file = __DIR__.'/../'.str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = $file.'.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 *  Route.
 */
require_once __DIR__.'/../route/web.php';


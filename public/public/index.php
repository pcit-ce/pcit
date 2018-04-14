<?php

declare(strict_types=1);

session_start();

function open_error()
{
    ini_set('display_errors', 'on');
    ini_set('error_reporting', '32767');
}

require_once __DIR__.'/../../vendor/autoload.php';

/**
 * read .env.* file.
 */
$env = new Dotenv\Dotenv(__DIR__.'/../', '.env'.'.'.getenv('APP_ENV'));

$env->load();

/*
 *  SPL Autoload
 */

$debug = getenv('CI_DEBUG') ?? false;

true === $debug && open_error();

spl_autoload_register(function ($class): void {
    $class = str_replace('App\\Http', 'app\\Http', $class);
    $file = __DIR__.'/../'.str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require_once $file.'.php';
});

/**
 *  Route.
 */
require_once __DIR__.'/../route/web.php';

//header('Location: https://ci2.khs1994.com/index.html');

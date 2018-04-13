<?php

declare(strict_types=1);

use KhsCI\Support\Route;

require_once __DIR__.'/../../vendor/autoload.php';

$env = new Dotenv\Dotenv(__DIR__.'/../', '.env'.'.'.getenv('APP_ENV'));

$env->load();

spl_autoload_register(function ($class) {
    $file = __DIR__.'/../'.str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require_once $file.'.php';
});

require_once __DIR__.'/../route/web.php';

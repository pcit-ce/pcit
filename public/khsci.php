#!/usr/bin/env php

<?php

use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../vendor/autoload.php';

spl_autoload_register(function ($class): void {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require __DIR__.DIRECTORY_SEPARATOR.$class.'.php';
});

spl_autoload_register(function ($class): void {
    $class = str_replace('App\\Http', 'app\\Http', $class);
    $class = str_replace('App\\Console', 'app\\Console', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require __DIR__.DIRECTORY_SEPARATOR.$class.'.php';
});

$env = new Dotenv\Dotenv(__DIR__, '.env'.'.'.getenv('APP_ENV'));

$env->load();

date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

/**
 * @see https://juejin.im/entry/5a3795a051882572ed55af00
 * @see https://segmentfault.com/a/1190000005084734
 */
$cli = new Application('KhsCI CLI', 'v18.05');

$cli->add(new CLI\Queue());

$cli->add(new CLI\Migrate());

$cli->add(new CLI\Up());

try {
    $cli->run();
} catch (Exception $e) {
    echo $e->getMessage();
    echo '';
    echo $e->getCode();
}

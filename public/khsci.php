#!/usr/bin/env php

<?php

require __DIR__.'/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require __DIR__.DIRECTORY_SEPARATOR.$class.'.php';
});

use Symfony\Component\Console\Application;

$env = new Dotenv\Dotenv(__DIR__, '.env'.'.'.getenv('APP_ENV'));

$env->load();

/**
 * @see https://juejin.im/entry/5a3795a051882572ed55af00
 *
 * @see https://segmentfault.com/a/1190000005084734
 */
$cli = new Application('KhsCI CLI', 'v18.05');

$cli->add(new CLI\Queue());

$cli->add(new CLI\Migrations());

try {
    $cli->run();
} catch (Exception $e) {

}

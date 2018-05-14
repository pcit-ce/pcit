#!/usr/bin/env php

<?php

use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../vendor/autoload.php';

spl_autoload_register(function ($class): void {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__.DIRECTORY_SEPARATOR.$class.'.php';

    if (file_exists($file)) {
        require $file;
    }
});

spl_autoload_register(function ($class): void {
    $class = str_replace('App\\', 'app\\', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    require __DIR__.'/../public/'.$class.'.php';
});

$env = new Dotenv\Dotenv(__DIR__.'/../public', '.env'.'.'.getenv('APP_ENV'));

$env->load();

date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

/**
 * @see https://juejin.im/entry/5a3795a051882572ed55af00
 * @see https://segmentfault.com/a/1190000005084734
 */
$cli = new Application('KhsCI CLI', 'v18.05');

$cli->add(new Queue());

$cli->add(new Migrate());

$cli->add(new Up());

try {
    $cli->run();
} catch (Exception $e) {
    throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
}

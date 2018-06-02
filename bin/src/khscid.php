#!/usr/bin/env php

<?php

// khscid.php is KhsCI Daemon CLI

use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

spl_autoload_register(function ($class): void {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__.DIRECTORY_SEPARATOR.$class.'.php';

    if (file_exists($file)) {
        require $file;
    }
});

try {
    $env_file = '.env';

    if (Env::get('APP_ENV')) {
        $env_file = '.env.'.Env::get('APP_ENV');
    }

    (new \Dotenv\Dotenv(__DIR__.'/../../public', $env_file))->load();

    date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

    /**
     * @see https://juejin.im/entry/5a3795a051882572ed55af00
     * @see https://segmentfault.com/a/1190000005084734
     */
    $cli = new Application('KhsCI Daemon CLI', 'v18.06');

    $cli->add(new Migrate());

    $cli->add(new Up());

    $cli->run();
} catch (Exception $e) {
    throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
}

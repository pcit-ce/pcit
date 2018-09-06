#!/usr/bin/env php

<?php

// khscid.php is KhsCI Daemon CLI

use App\Console\KhsCIDaemon\MigrateCommand;
use App\Console\KhsCIDaemon\UpCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/../../public/bootstrap/app.php';

try {
    /**
     * @see https://juejin.im/entry/5a3795a051882572ed55af00
     * @see https://segmentfault.com/a/1190000005084734
     */
    $cli = new Application('KhsCI Daemon CLI', 'v18.06');

    $cli->add(new MigrateCommand());

    $cli->add(new UpCommand());

    $cli->run();
} catch (Exception $e) {
    throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
}

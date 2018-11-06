#!/usr/bin/env php

<?php

use App\Console\PCITDaemon\AgentCommand;
use App\Console\PCITDaemon\MigrateCommand;
use App\Console\PCITDaemon\UpCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/../../framework/bootstrap/app.php';

try {
    /**
     * @see https://juejin.im/entry/5a3795a051882572ed55af00
     * @see https://segmentfault.com/a/1190000005084734
     */
    $cli = new Application('PCIT Daemon CLI', 'v18.06');

    $cli->add(new MigrateCommand());

    $cli->add(new UpCommand());

    $cli->add(new AgentCommand());

    $cli->run();
} catch (Exception $e) {
    throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
}

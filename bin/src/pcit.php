#!/usr/bin/env php

<?php

use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../framework/bootstrap/app.php';

$cli = new Application('PCIT CLI', 'v18.06');

$fh = opendir(base_path().'app/Console/PCIT/Repo');

while ($file = readdir($fh)) {
    if ('.' === $file or '..' === $file) {
        continue;
    }

    $class = '\App\Console\PCIT\Repo\\'.rtrim($file, '.php');

    $cli->add(new $class());
}

$fh = opendir(base_path().'app/Console/PCIT');

if ($fh) {
    while (false !== ($file = readdir($fh))) {
        if ('.' === $file or '..' === $file or 'PCITCommand.php' === $file or 'Repo' === $file) {
            continue;
        }

        $class = '\App\Console\PCIT\\'.rtrim($file, '.php');

        $cli->add(new $class());
    }
}

$cli->run();

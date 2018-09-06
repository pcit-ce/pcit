#!/usr/bin/env php

<?php

use Symfony\Component\Console\Application;

require __DIR__.'/../../public/bootstrap/app.php';

$cli = new Application('KhsCI CLI', 'v18.06');

$fh = opendir(__DIR__.'/../../public/app/Console/KhsCI/Repo');

while ($file = readdir($fh)) {
    if ('.' === $file or '..' === $file) {
        continue;
    }

    $class = '\App\Console\KhsCI\Repo\\'.rtrim($file, '.php');

    $cli->add(new $class());
}

$fh = opendir(__DIR__.'/../../public/app/Console/KhsCI');

if ($fh) {
    while (false !== ($file = readdir($fh))) {
        if ('.' === $file or '..' === $file or 'KhsCICommand.php' === $file or 'Repo' === $file) {
            continue;
        }

        $class = '\App\Console\KhsCI\\'.rtrim($file, '.php');

        $cli->add(new $class());
    }
}

$cli->run();

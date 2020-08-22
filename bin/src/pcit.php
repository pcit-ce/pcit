#!/usr/bin/env php

<?php

use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

putenv('CI_CACHE_DRIVE=none');

$app = require __DIR__.'/../../framework/bootstrap/app.php';

$cli = new Application('PCIT CLI', 'v19.12');

function getCommandFromDir($cli, $dir)
{
    $fh = opendir(base_path().'app/Console/PCIT/'.$dir);

    while ($file = readdir($fh)) {
        if ('.' === $file or '..' === $file) {
            continue;
        }

        $class = '\App\Console\PCIT\\'.$dir.'\\'.rtrim($file, '.php');

        $cli->add(new $class());
    }

    return $cli;
}

$cli = getCommandFromDir($cli, 'Repo');
$cli = getCommandFromDir($cli, 'Developer');

$fh = opendir(base_path().'app/Console/PCIT');

if ($fh) {
    while (false !== ($file = readdir($fh))) {
        if ('.' === $file or '..' === $file or 'PCITCommand.php' === $file or 'Repo' === $file) {
            continue;
        }

        if (is_dir(base_path().'app/Console/PCIT'.'/'.$file)) {
            continue;
        }

        $class = '\App\Console\PCIT\\'.rtrim($file, '.php');

        $cli->add(new $class());
    }
}

$cli->setCatchExceptions(false);
$cli->run();

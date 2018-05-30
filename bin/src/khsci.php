#!/usr/bin/env php

<?php

use App\Console\Khsci\Login;
use App\Console\Khsci\Logout;
use App\Console\Khsci\Repo\Env as EnvCommand;
use App\Console\Khsci\Token;
use App\Console\Khsci\Whoami;
use Dotenv\Dotenv;
use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

(new NunoMaduro\Collision\Provider())->register();

$env_file = '.env';

if (Env::get('APP_ENV')) {
    $env_file = '.env.'.Env::get('APP_ENV');
}

if (file_exists(__DIR__.'/../../public/'.$env_file)) {
    (new Dotenv(__DIR__.'/../../public', $env_file))->load();
}

date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

$cli = new Application('KhsCI CLI', 'v18.06');

$cli->add(new Login());

$cli->add(new Logout());

$cli->add(new Token());

$cli->add(new EnvCommand());

$cli->add(new Whoami());

$cli->run();

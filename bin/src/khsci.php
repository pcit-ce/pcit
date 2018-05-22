#!/usr/bin/env php

<?php

use App\Console\Khsci\Login;
use App\Console\Khsci\Logout;
use Dotenv\Dotenv;
use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';


(new Dotenv(__DIR__.'/../../public/', '.env.'.Env::get('APP_ENV')))->load();

(new NunoMaduro\Collision\Provider())->register();

date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

$cli = new Application('KhsCI CLI', 'v18.06');

$cli->add(new Login());

$cli->add(new Logout());

$cli->run();


#!/usr/bin/env php

<?php

use App\Console\Khsci\InitCommand;
use App\Console\Khsci\LoginCommand;
use App\Console\Khsci\LogoutCommand;
use App\Console\Khsci\Repo\EnvCommand;
use App\Console\Khsci\Repo\SettingCommand;
use App\Console\Khsci\SyncCommand;
use App\Console\Khsci\TokenCommand;
use App\Console\Khsci\WhoamiCommand;
use Dotenv\Dotenv;
use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

'true' === Env::get('CI_DEBUG', false) && \KhsCI\Support\CI::enableDebug();

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

$cli->add(new LoginCommand());

$cli->add(new LogoutCommand());

$cli->add(new TokenCommand());

$cli->add(new EnvCommand());

$cli->add(new WhoamiCommand());

$cli->add(new SettingCommand());

$cli->add(new InitCommand());

$cli->add(new SyncCommand());

$cli->run();

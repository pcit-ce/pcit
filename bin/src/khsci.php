#!/usr/bin/env php

<?php

use App\Console\KhsCI\InitCommand;
use App\Console\KhsCI\LoginCommand;
use App\Console\KhsCI\LogoutCommand;
use App\Console\KhsCI\Repo\EnvCommand;
use App\Console\KhsCI\Repo\SettingCommand;
use App\Console\KhsCI\SyncCommand;
use App\Console\KhsCI\TokenCommand;
use App\Console\KhsCI\WhoamiCommand;
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

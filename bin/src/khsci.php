#!/usr/bin/env php

<?php

use App\Console\KhsCI\InitCommand;
use App\Console\KhsCI\LoginCommand;
use App\Console\KhsCI\LogoutCommand;
use App\Console\KhsCI\Repo\EnvCommand;
use App\Console\KhsCI\Repo\SettingsCommand;
use App\Console\KhsCI\SyncCommand;
use App\Console\KhsCI\TokenCommand;
use App\Console\KhsCI\WhoamiCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/../../public/bootstrap/app.php';

$cli = new Application('KhsCI CLI', 'v18.06');

$cli->add(new LoginCommand());

$cli->add(new LogoutCommand());

$cli->add(new TokenCommand());

$cli->add(new EnvCommand());

$cli->add(new WhoamiCommand());

$cli->add(new SettingsCommand());

$cli->add(new InitCommand());

$cli->add(new SyncCommand());

$cli->run();

#!/usr/bin/env php

<?php

use App\Console\TencentAI\Chat;
use App\Console\TencentAI\Translate;
use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

$cli = new Application('Tencent AI CLI', 'v18.06');

try {
    (new \Dotenv\Dotenv(__DIR__.'/../../public', '.env.'.Env::get('APP_ENV')))->load();

    (new \NunoMaduro\Collision\Provider())->register();

    $cli->add(new Translate());

    $cli->add(new Chat());

    $cli->run();
} catch (Throwable $e) {
    throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
}

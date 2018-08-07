#!/usr/bin/env php

<?php

use App\Console\TencentAI\ChatCommand;
use App\Console\TencentAI\OCRCommand;
use App\Console\TencentAI\TranslateCommand;
use KhsCI\Support\Env;
use Symfony\Component\Console\Application;

require __DIR__.'/../../vendor/autoload.php';

'true' === Env::get('CI_DEBUG', false) && \KhsCI\Support\CI::enableDebug();

$cli = new Application('Tencent AI CLI', 'v18.06');

(new \NunoMaduro\Collision\Provider())->register();

$env_file = '.env';

if (Env::get('APP_ENV')) {
    $env_file = '.env.'.Env::get('APP_ENV');
}

if (!file_exists(__DIR__.'/../../public/'.$env_file)) {
    throw new Exception('Please SET env vars in '.__DIR__.'/../../public/'.$env_file, 404);
}

(new \Dotenv\Dotenv(__DIR__.'/../../public', $env_file))->load();

$cli->add(new TranslateCommand());

$cli->add(new ChatCommand());

$cli->add(new OCRCommand());

$cli->run();

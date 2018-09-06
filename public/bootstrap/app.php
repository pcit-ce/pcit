<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use KhsCI\Support\CI;
use KhsCI\Support\Env;

require __DIR__.'/../../vendor/autoload.php';

if ('cli' !== \PHP_SAPI) {
    (new NunoMaduro\Collision\Provider())->register();
}

'true' === Env::get('CI_DEBUG', false) && CI::enableDebug();

$env_file = '.env';

if ($app_env = Env::get('APP_ENV')) {
    $env_file = '.env.'.$app_env;
}

if (file_exists(__DIR__.'/../../public/'.$env_file)) {
    (new Dotenv(__DIR__.'/../../public', $env_file))->load();
}

date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

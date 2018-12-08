<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use PCIT\Support\CI;
use PCIT\Support\Env;

require __DIR__.'/../../vendor/autoload.php';

if ('cli' === \PHP_SAPI) {
    (new NunoMaduro\Collision\Provider())->register();
}

'true' === env('CI_DEBUG', false) && CI::enableDebug();

$app_env = CI::environment();

$env_file = $app_env ? '.env.'.$app_env : '.env';

file_exists(base_path().$env_file) && (new Dotenv(base_path(), $env_file))->load();

date_default_timezone_set(env('CI_TZ', 'PRC'));

$app = new \PCIT\Foundation\Application([]);

$app[\App\Http\Kernel::class] = function ($app) {
    return new \App\Http\Kernel();
};

return $app;

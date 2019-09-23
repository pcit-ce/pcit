<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use PCIT\Support\CI;
use PCIT\Support\Env;

if ('cli' === \PHP_SAPI) {
    (new NunoMaduro\Collision\Provider())->register();
}

// class alias
foreach (config('app.alias') as $key => $value) {
    if (!class_exists($value) or class_exists($key)) {
        continue;
    }
    class_alias($value, $key);
}

$app_env = CI::environment();

$env_file = $app_env ? '.env.'.$app_env : '.env';

$env_file = file_exists(base_path().$env_file) ? $env_file : '.env';

Dotenv::create(base_path(), $env_file)->load();

date_default_timezone_set(env('CI_TZ', 'PRC'));

$debug = config('app.debug');

if ($debug) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();

    CI::enableDebug();
}

$app = new \PCIT\Framework\Foundation\Application([]);
$app->environmentFile = $env_file;
$app->environmentPath = base_path().$env_file;

$app->singleton(\App\Http\Kernel::class, function ($app) {
    return new \App\Http\Kernel();
});

return $app;

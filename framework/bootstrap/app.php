<?php

declare(strict_types=1);

use PCIT\Framework\Dotenv\Dotenv;
use PCIT\Framework\Foundation\AliasLoader;
use PCIT\Support\CI;
use PCIT\Support\Env;

// cli error handler
if ('cli' === \PHP_SAPI) {
    (new NunoMaduro\Collision\Provider())->register();
}

// class alias
AliasLoader::load(config('app.alias'));

// load env file
$app_env = CI::environment();

$env_file = Dotenv::load($app_env);

// set timezone
date_default_timezone_set(env('CI_TZ', 'PRC'));

// set web error handler
$debug = config('app.debug');

if ($debug) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();

    CI::enableDebug();
}

// get app
$app = new \PCIT\Framework\Foundation\Application([]);

// 绑定单例
$app->singleton(\App\Http\Kernel::class, function ($app) {
    return new \App\Http\Kernel();
});

$app->environmentFile = $env_file;
$env_file && $app->environmentPath = base_path().$env_file;

return $app;

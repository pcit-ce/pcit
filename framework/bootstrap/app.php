<?php

declare(strict_types=1);

use PCIT\Framework\Foundation\AliasLoader;
use PCIT\Support\CI;

// cli error handler
if ('cli' === \PHP_SAPI) {
    (new NunoMaduro\Collision\Provider())->register();
}

// get app
$app = new \PCIT\Framework\Foundation\Application(['base_path' => dirname(dirname(__DIR__))]);

// class alias
AliasLoader::load(config('app.alias'));

// set timezone
date_default_timezone_set(env('CI_TZ', 'PRC'));

// set web error handler
// don't enable on cli
if ($app->isDebug && \PHP_SAPI !== 'cli') {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();

    CI::enableDebug();
}

// 绑定单例
$app->singleton(\App\Http\Kernel::class, function ($app) {
    return new \App\Http\Kernel();
});

return $app;

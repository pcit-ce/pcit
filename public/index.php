<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;

ob_start();

define('PCIT_START', microtime(true));

$app = require __DIR__.'/../framework/bootstrap/app.php';

// 从容器中解析出 Http Kernel 实例
$kernel = $app->make(\App\Http\Kernel::class);

$response = $kernel->handle($request = Request::createFromGlobals());

$response->send();

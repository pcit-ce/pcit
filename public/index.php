<?php

declare(strict_types=1);

ob_start();

define('KHSCI_START', microtime(true));

use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Route;

require __DIR__.'/bootstrap/app.php';

ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.'.getenv('CI_SESSION_DOMAIN'));
ini_set('session.gc_maxlifetime', '690000'); // s
ini_set('session.cookie_lifetime', '690000'); // s
ini_set('session.cookie_secure', 'On');

// session_set_cookie_params(1800 , '/', '.'getenv('CI_SESSION_DOMAIN', true));

// Open Debug?

$debug = true === Env::get('CI_DEBUG', false);

// SPL Autoload

if ('/index.php' === $_SERVER['REQUEST_URI']) {
    Response::redirect('dashboard');
    exit;
}

// Route.

try {
    require_once __DIR__.'/route/web.php';
} catch (Exception | Error $e) {
    if ('Finish' === $e->getMessage()) {
        $output = Route::$output;

        switch (gettype($output)) {
            case 'array':
                Response::json($output, KHSCI_START);

                break;
            case 'integer':
                echo $output;

                break;
            case 'string':
                echo $output;

                break;
        }
        exit;
    }

    Response::json([
        'code' => $e->getCode() ?? 500,
        'message' => $e->getMessage() ?? 'ERROR',
        'documentation_url' => 'https://github.com/khs1994-php/khsci/tree/master/docs/api',
    ], KHSCI_START);

    exit;
}

// 路由控制器填写错误

if ('true' === $debug) {
    Response::json([
        'code' => 404,
        'obj' => Route::$obj ?? null,
        'method' => Route::$method ?? null,
        'message' => 'Route Not Found',
        'api_url' => getenv('CI_HOST').'/api',
    ], KHSCI_START);
} else {
    Response::json([
        'code' => 404,
        'message' => 'Not Found',
        'api_url' => getenv('CI_HOST').'/api',
    ], KHSCI_START);
}

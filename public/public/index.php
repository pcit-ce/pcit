<?php

declare(strict_types=1);
require_once __DIR__.'/../../vendor/autoload.php';

use KhsCI\KhsCI;

$env = new Dotenv\Dotenv(__DIR__.'/../', '.env'.'.'.getenv('APP_ENV'));

$env->load();

$config = [
    'coding' => [
        'client_id' => getenv('CODING_CLIENT_ID'),
        'client_secret' => getenv('CODING_CLIENT_SECRET'),
        'callback_url' => getenv('CODING_CALLBACK_URL'),
    ],
    'gitee' => [
        'client_id' => getenv('GITEE_CLIENT_ID'),
        'client_secret' => getenv('GITEE_CLIENT_SECRET'),
        'callback_url' => getenv('GITEE_CALLBACK_URL'),
    ],
    'github' => [
        'client_id' => getenv('GITHUB_CLIENT_ID'),
        'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
        'callback_url' => getenv('GITHUB_CALLBACK_URL'),
    ],
];

$khsci = new KhsCI($config);

if ($_GET['code']) {
    echo $khsci->OAuthCoding->getAccessToken();
    exit(1);
}

$khsci->OAuthCoding->getLoginUrl();


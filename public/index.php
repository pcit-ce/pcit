<?php

require_once __DIR__.'/../vendor/autoload.php';

use KhsCI\KhsCI;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

//$khsci = new KhsCI();

//\KhsCI\Support\Config::makeOAuthCodingArray();

$config = [
    'coding' => [
        'client_id' => getenv('CODING_CLIENT_ID'),
        'client_secret' => getenv('CODING_CLIENT_SECRET'),
        'callback_url' => getenv('CODING_CALLBACK_URL')
    ],
    'gitee' => [
        'client_id' => getenv('GITEE_CLIENT_ID'),
        'client_secret' => getenv('GITEE_CLIENT_SECRET'),
        'callback_url' => getenv('GITEE_CALLBACK_URL')
    ],
    'github' => [
        'client_id' => getenv('GITHUB_CLIENT_ID'),
        'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
        'callback_url' => getenv('GITHUB_CALLBACK_URL')
    ],
];

var_dump($config);

//$khsci->OauthCoding->getAccessToken();
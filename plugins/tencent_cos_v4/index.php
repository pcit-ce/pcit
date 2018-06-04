<?php

declare(strict_types=1);
require __DIR__.'/vendor/autoload.php';

use QCloud\Cos\Api;

$config = [
    'app_id' => getenv('COS_APP_ID'),
    'secret_id' => getenv('COS_SECRET_ID'),
    'secret_key' => getenv('COS_SECRET_KEY'),
    'region' => getenv('COS_REGION'),
    'timeout' => 60,
];

try {
    $cosApi = new Api($config);

    foreach (json_decode(getenv('COS_FILE'), true) as $file => $label) {
        $ret = $cosApi->upload(getenv('COS_BUCKET'), $file, $label);

        var_dump($ret);
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

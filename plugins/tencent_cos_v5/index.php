<?php

declare(strict_types=1);
require __DIR__.'/vendor/autoload.php';

try {
    $cosClient = new Qcloud\Cos\Client([
        'region' => getenv('COS_REGION'),
        'credentials' => [
            'appId' => getenv('COS_APP_ID'),
            'secretId' => getenv('COS_KEY'),
            'secretKey' => getenv('COS_SECRET'),
        ],
    ]);

    foreach (json_decode(getenv('COS_FILE'), true) as $file => $label) {
        $result = $cosClient->putObject([
            'Bucket' => getenv('COS_BUCKET'),
            'Key' => $label,
            'Body' => fopen($file, 'rb'),
        ]);

        var_dump($result);
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

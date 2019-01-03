<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

try {
    $cosClient = new Qcloud\Cos\Client([
        'region' => getenv('COS_V5_REGION'),
        'credentials' => [
            'appId' => getenv('COS_V5_APP_ID'),
            'secretId' => getenv('COS_V5_KEY'),
            'secretKey' => getenv('COS_V5_SECRET'),
        ],
    ]);

    foreach (json_decode(getenv('COS_V5_FILE'), true) as $file => $label) {
        $result = $cosClient->putObject([
            'Bucket' => getenv('COS_V5_BUCKET'),
            'Key' => $label,
            'Body' => fopen($file, 'r'),
        ]);

        var_dump($result);
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

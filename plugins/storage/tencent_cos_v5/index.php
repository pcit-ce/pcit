<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

try {
    $cosClient = new Qcloud\Cos\Client([
        'region' => getenv('INPUT_COS_V5_REGION'),
        'credentials' => [
            'appId' => getenv('INPUT_COS_V5_APP_ID'),
            'secretId' => getenv('INPUT_COS_V5_KEY'),
            'secretKey' => getenv('INPUT_COS_V5_SECRET'),
        ],
    ]);

    foreach (json_decode(getenv('INPUT_COS_V5_FILE'), true) as $file => $label) {
        $result = $cosClient->putObject([
            'Bucket' => getenv('INPUT_COS_V5_BUCKET'),
            'Key' => $label,
            'Body' => fopen($file, 'r'),
        ]);

        var_dump($result);
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

<?php

require __DIR__.'/vendor/autoload.php';

try {
    $cosClient = new Qcloud\Cos\Client([
        'region' => getenv('COS_REGION'),
        'credentials' => [
            'appId' => getenv('COS_APP_ID'),
            'secretId' => getenv('COS_KEY'),
            'secretKey' => getenv('COS_SECRET')
        ]
    ]);

    $result = $cosClient->putObject([
        'Bucket' => getenv('COS_BUCKET'),
        'Key' => getenv('COS_LABEL'),
        'Body' => fopen(getenv('COS_FILE'), 'rb'),
    ]);

    var_dump($result);

} catch (Throwable $e) {
    echo $e->__toString();
}

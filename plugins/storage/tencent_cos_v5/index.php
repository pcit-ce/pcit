<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

try {
    $cosClient = new Qcloud\Cos\Client([
        'region' => getenv('INPUT_REGION'),
        'credentials' => [
            'appId' => getenv('INPUT_APP_ID'),
            'secretId' => getenv('INPUT_KEY'),
            'secretKey' => getenv('INPUT_SECRET'),
        ],
    ]);

    foreach (json_decode(getenv('INPUT_FILE'), true) as $file => $label) {
        $result = $cosClient->putObject([
            'Bucket' => getenv('INPUT_BUCKET'),
            'Key' => $label,
            'Body' => fopen($file, 'r'),
        ]);

        var_dump($result);
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$prefix = getenv('INPUT_PREFIX') ? getenv('INPUT_PREFIX').DIRECTORY_SEPARATOR : false;

try {
    $cosClient = new Qcloud\Cos\Client([
        'region' => getenv('INPUT_REGION'),
        'credentials' => [
            'appId' => getenv('INPUT_APP_ID'),
            'secretId' => getenv('INPUT_SECRET_ID'),
            'secretKey' => getenv('INPUT_SECRET_KEY'),
        ],
    ]);

    $input_files = getenv('INPUT_FILES');

    // obj
    if (is_object(json_decode($input_files))) {
        foreach (json_decode($input_files, true) as $file => $key) {
            $key = $prefix ? ($prefix.$key) : $key;
            $result = $cosClient->putObject([
                'Bucket' => getenv('INPUT_BUCKET'),
                'Key' => $key,
                'Body' => fopen($file, 'r'),
            ]);

            echo "===> Upload $file TO $key result\n";

            var_dump($result);
        }
        // array
    } else {
        $files = explode(',', $input_files);

        foreach ($files as $file) {
            $key = $prefix ? ($prefix.$file) : $file;
            $result = $cosClient->putObject([
                'Bucket' => getenv('INPUT_BUCKET'),
                'Key' => $key,
                'Body' => fopen($file, 'r'),
            ]);

            echo "===> Upload $file TO $key result\n";

            var_dump($result);
        }
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

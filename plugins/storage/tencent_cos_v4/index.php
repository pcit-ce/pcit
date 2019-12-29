<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use League\Flysystem\COSV4\COSV4Adapter;
use League\Flysystem\COSV4\COSV4Client;
use League\Flysystem\Filesystem;

$config = [
    'app_id' => getenv('INPUT_APP_ID'),
    'secret_id' => getenv('INPUT_SECRET_ID'),
    'secret_key' => getenv('INPUT_SECRET_KEY'),
    'region' => getenv('INPUT_REGION'),
    'timeout' => 60,
];

$bucket = getenv('INPUT_BUCKET');
$prefix = getenv('INPUT_PREFIX');

try {
    $client = new COSV4Client($config);
    $adapter = new COSV4Adapter($client, $bucket, $prefix);
    $flysystem = new Filesystem($adapter);

    $input_files = getenv('INPUT_FILES');

    if (is_object(json_decode($input_files))) {
        foreach (json_decode($input_files, true) as $file => $label) {
            $result = $flysystem->write($label, file_get_contents($file));

            echo "===> Upload $file TO $label result\n";
            var_dump($result);
            echo "\n";
        }
    } else {
        $files = explode(',', $input_files);

        foreach ($files as $file) {
            $result = $flysystem->write($file, file_get_contents($file));

            echo "===> Upload $file TO $file result\n";
            var_dump($result);
            echo "\n";
        }
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use League\Flysystem\COSV4\COSV4Client;
use League\Flysystem\Filesystem;
use League\Flysysyem\COSv4\COSV4Adapter;

$config = [
    'app_id' => getenv('PCIT_COS_V4_APP_ID'),
    'secret_id' => getenv('PCIT_COS_V4_SECRET_ID'),
    'secret_key' => getenv('PCIT_COS_V4_SECRET_KEY'),
    'region' => getenv('PCIT_COS_V4_REGION'),
    'timeout' => 60,
];

$bucket = getenv('PCIT_COS_V4_BUCKET');
$prefix = getenv('PCIT_COS_V4_PREFIX');

try {
    $client = new COSV4Client($config);
    $adapter = new COSV4Adapter($config, $bucket, $prefix);
    $filesystem = new Filesystem($adapter);

    foreach (json_decode(getenv('PCIT_COS_V4_FILE'), true) as $file => $label) {
        $result = $flysystem->write($label, file_get_contents($file));

        echo "===> Upload $file to $label result";
        var_dump($result);
        echo "\n";
    }
} catch (Throwable $e) {
    echo $e->__toString();
}

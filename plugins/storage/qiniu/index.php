<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use PCIT\Plugin\Toolkit\Core;

$core = new Core();

$config = [
    'app_id' => getenv('INPUT_APP_ID'),
    'secret_id' => getenv('INPUT_SECRET_ID'),
    'secret_key' => getenv('INPUT_SECRET_KEY'),
    'region' => getenv('INPUT_REGION'),
    'timeout' => 60,
];

$bucket = getenv('INPUT_BUCKET');
$endpoint = getenv('INPUT_ENDPOINT');
$accessKey = getenv('INPUT_ACCESS_KEY');
$secretKey = getenv('INPUT_SECRET_KEY');

try {
    $adapter = new QiniuAdapter($accessKey, $secretKey, $bucket, $endpoint);

    $flysystem = new Filesystem($adapter);

    $input_files = getenv('INPUT_FILES');

    // obj
    if (is_object(json_decode($input_files))) {
        foreach (json_decode($input_files, true) as $file => $label) {
            $result = $flysystem->write($label, file_get_contents($file));

            $core->debug("Upload [ $file ] TO [ $label ]");
            $core->debug((string) $result);
        }
        // array
    } else {
        $files = explode(',', $input_files);

        foreach ($files as $file) {
            $result = $flysystem->write($file, file_get_contents($file));

            $core->debug("Upload [ $file ] TO [ $file ]");
            $core->debug((string) $result);
        }
    }
} catch (Throwable $e) {
    $core->debug($e->__toString());
}

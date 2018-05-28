<?php

require __DIR__.'/vendor/autoload.php';

use QCloud\Cos\Api;

$config = array(
    'app_id' => getenv('COS_APP_ID'),
    'secret_id' => getenv('COS_SECRET_ID'),
    'secret_key' => getenv('COS_SECRET_KEY'),
    'region' => getenv('COS_REGION'),
    'timeout' => 60
);

try {
    $cosApi = new Api($config);

    $ret = $cosApi->upload(getenv('COS_BUCKET'), getenv('COS_FILE'), getenv('COS_LABEL'));

    var_dump($ret);

} catch (Throwable $e) {

    echo $e->__toString();
}

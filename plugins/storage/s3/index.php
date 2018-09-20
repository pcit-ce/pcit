<?php

declare(strict_types=1);

use League\Flysystem\AwsS3v3\AwsS3Adapter;

require __DIR__.'/vendor/autoload.php';

$options = [
    'version' => 'latest',
    'region' => getenv('S3_REGION'),
    'endpoint' => getenv('S3_ENDPOINT'),
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key' => getenv('S3_ACCESSKEYID'),
        'secret' => getenv('S3_SECRETACCESSKEY'),
    ],
];

$flysystem = new League\Flysystem\Filesystem(new AwsS3Adapter(new \Aws\S3\S3Client($options), getenv('S3_BUCKET')));

if ($s3_cache = getenv('S3_CACHE')) {
    $prefix = getenv('S3_CACHE_PREFIX');

    if (getenv('S3_CACHE_DOWNLOAD')) {
        echo 'Setting up build cache';

        file_put_contents($prefix, $flysystem->get($prefix.'.tar.gz'));
        exec("set -ex ; tar -zxvf {$prefix}.tar.gz ; rm -rf -zxvf {$prefix}");
        exit;
    }

    $file_list = null;

    echo 'store build cache';

    foreach ((array) json_decode($s3_cache) as $item) {
        $file_list .= ' '.$item;
    }

    $file_list = trim($file_list, ' ');

    exec("set -ex ; tar -zcvf {$prefix}.tar.gz $file_list ; rm -rf {$prefix}.tar.gz");

    $result = $flysystem->put($prefix.'.tar.gz', file_get_contents($prefix.'.tar.gz'));

    echo 'result is '.$result ? 'success' : 'failure';

    exit;
}

foreach (json_decode(getenv('S3_FILE')) as $item) {
    foreach ($item as $k => $v) {
        $flysystem->put($v, file_get_contents($k));
    }
}

<?php

declare(strict_types=1);

use League\Flysystem\Adapter\Local;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

require __DIR__.'/vendor/autoload.php';

echo "\n\n===> use PCIT Plugin s3\n\n";

$options = [
    'version' => 'latest',
    'region' => getenv('PCIT_S3_REGION'),
    'endpoint' => getenv('PCIT_S3_ENDPOINT'),
    'use_path_style_endpoint' => 'true' === getenv('PCIT_S3_USE_PATH_STYLE_ENDPOINT'),
    'credentials' => [
        'key' => getenv('PCIT_S3_ACCESS_KEY_ID'),
        'secret' => getenv('PCIT_S3_SECRET_ACCESS_KEY'),
    ],
    'http' => [
        'connect_timeout' => getenv('PCIT_S3_CONNECT_TIMEOUT') ?: 20,
    ],
];

$bucket = getenv('S3_BUCKET') ?: 'pcit';

$flysystem = new Filesystem(
    new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket));

// handle cache
if ($s3_cache = getenv('PCIT_S3_CACHE')) {
    $prefix = getenv('PCIT_S3_CACHE_PREFIX');
    $cache_tar_gz_name = $prefix.'.tar.gz';

    if (getenv('PCIT_S3_CACHE_DOWNLOAD')) {
        echo "\n\n==> Setting up build cache\n";

        try {
            file_put_contents($cache_tar_gz_name, $flysystem->read($cache_tar_gz_name));

            exec("set -ex ; tar -zxvf $cache_tar_gz_name ; rm -rf $cache_tar_gz_name");
        } catch (\League\Flysystem\FileNotFoundException $e) {
            echo $e->getMessage().'. Code is '.$e->getCode()."\n";
        }

        exit;
    }

    $file_list = null;

    echo "\n\n==> Store build cache\n";

    foreach ((array) json_decode($s3_cache) as $item) {
        $file_list .= ' '.$item;
    }

    $file_list = trim($file_list, ' ');

    exec("set -ex ; tar -zcvf $cache_tar_gz_name $file_list");

    $result = $flysystem->put($cache_tar_gz_name, file_get_contents($cache_tar_gz_name));

    exec("rm -rf $cache_tar_gz_name");

    echo $result ? 'success' : 'failure';

    exit;
} // handle cache end

if (getenv('PCIT_S3_FILE')) {
    foreach (json_decode(getenv('PCIT_S3_FILE')) as $item) {
        foreach ($item as $k => $v) {
            $flysystem->put($v, file_get_contents($k));
        }
    }
}

// local_dir upload_dir
$local_dir = getenv('PCIT_S3_LOCAL_DIR');
$upload_dir = getenv('PCIT_S3_UPLOAD_DIR');

$local_dir = '/' === $local_dir ? '/' : trim($local_dir, '/');
$upload_dir = '/' === $upload_dir ? '/' : trim($upload_dir, '/');

if (!($local_dir && $upload_dir)) {
    exit;
}

if (is_file($local_dir)) {
    $flysystem->put($upload_dir, file_get_contents($local_dir));

    exit;
}

$adapter = new Local(getcwd());
$localFilesystem = new Filesystem($adapter);

$contents = $localFilesystem->listContents($local_dir, true);

$length = '/' === $local_dir ? '-1' : strlen($local_dir);

foreach ($contents as $key => $value) {
    if ('file' === $value['type']) {
        $local_file = $value['path'];

        $upload_file = substr($local_file, $length + 1);
        $upload_file = $upload_dir.'/'.$upload_file;
        $upload_file = trim($upload_file, '/');

        $flysystem->put($upload_file, file_get_contents($local_file));

        echo "\n===> Upload $local_file => $upload_file \n";
    }
}

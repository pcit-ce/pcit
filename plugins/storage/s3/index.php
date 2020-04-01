<?php

declare(strict_types=1);

use League\Flysystem\Adapter\Local;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

require __DIR__.'/vendor/autoload.php';

echo "\n\n===> use PCIT Plugin s3\n\n";

$options = [
    'version' => 'latest',
    'region' => getenv('INPUT_REGION'),
    'endpoint' => getenv('INPUT_ENDPOINT'),
    'use_path_style_endpoint' => 'true' === getenv('INPUT_USE_PATH_STYLE_ENDPOINT'),
    'credentials' => [
        'key' => getenv('INPUT_ACCESS_KEY_ID'),
        'secret' => getenv('INPUT_SECRET_ACCESS_KEY'),
    ],
    'http' => [
        'connect_timeout' => getenv('INPUT_CONNECT_TIMEOUT') ?: 20,
    ],
];

$bucket = getenv('INPUT_BUCKET') ?: 'pcit-caches';

$flysystem = new Filesystem(
    new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket));

// handle artifact

if ($artifact_name = getenv('INPUT_ARTIFACT_NAME')) {
    $s3_path_root = getenv('INPUT_UPLOAD_DIR');
    $local_path = getenv('INPUT_ARTIFACT_PATH');

    if (getenv('INPUT_ARTIFACT_DOWNLOAD')) {
        // tar -zxvf .tar.gz -C local_path
        exit;
    }

    // tar -zcvf .tar.gz local_path

    $tar_gz_name = $artifact_name.'.tar.gz';

    exec("set -ex ; tar -zcvf $tar_gz_name $local_path");

    $result = $flysystem->put($s3_path_root.'/'.$tar_gz_name, file_get_contents($tar_gz_name));

    exec("rm -rf $tar_gz_name");

    echo $result ? 'success' : 'failure';

    exit;
}

// handle cache
if ($s3_cache = getenv('INPUT_CACHE')) {
    $prefix = getenv('INPUT_CACHE_PREFIX');
    $s3_path = $prefix.'.tar.gz';
    $cache_tar_gz_name = explode('/', $prefix)[3].'.tar.gz';

    if (getenv('INPUT_CACHE_DOWNLOAD')) {
        echo "\n\n==> Setting up build cache\n";

        try {
            file_put_contents($cache_tar_gz_name, $flysystem->read($s3_path));

            exec("set -ex ; tar -zxvf $cache_tar_gz_name ; rm -rf $cache_tar_gz_name");
        } catch (\League\Flysystem\FileNotFoundException $e) {
            echo $e->getMessage().'. Code is '.$e->getCode()."\n";
        }

        exit;
    }

    $file_list = null;

    echo "\n\n==> Store build cache\n";

    $file_list = str_replace(',', ' ', $s3_cache);

    $file_list = trim($file_list, ' ');

    exec("set -ex ; tar -zcvf $cache_tar_gz_name $file_list");

    $result = $flysystem->put($s3_path, file_get_contents($cache_tar_gz_name));

    exec("rm -rf $cache_tar_gz_name");

    echo $result ? 'success' : 'failure';

    exit;
} // handle cache end

if (getenv('INPUT_FILES')) {
    $input_files = getenv('INPUT_FILES');

    if (is_object(json_decode($input_files))) {
        foreach (json_decode($input_files, true) as $file => $s3_file) {
            $flysystem->put($s3_file, file_get_contents($file));

            echo "\n===> Upload $file TO $s3_file \n";
        }
    } else {
        $files = explode(',', $input_files);
        foreach ($files as $file) {
            $flysystem->put($file, file_get_contents($file));

            echo "\n===> Upload $file TO $file \n";
        }
    }
}

// local_dir upload_dir
$local_dir = getenv('INPUT_LOCAL_DIR');
$upload_dir = getenv('INPUT_UPLOAD_DIR');

// upload_dir 为空
$upload_dir = $upload_dir ? $upload_dir : $local_dir;

if (!($local_dir && $upload_dir)) {
    exit;
}

$local_dir = '/' === $local_dir ? '/' : trim($local_dir, '/');
$upload_dir = '/' === $upload_dir ? '/' : trim($upload_dir, '/');

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

        echo "\n===> Upload $local_file TO $upload_file \n";
    }
}

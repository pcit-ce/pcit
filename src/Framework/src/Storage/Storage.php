<?php

declare(strict_types=1);

namespace PCIT\Framework\Storage;

use Etime\Flysystem\Plugin\AWS_S3 as AWS_S3_Plugin;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class Storage
{
    public $flysystem;

    public function __construct()
    {
        $bucket = config('filesystems.bucket');

        $options = config('filesystems.disks.s3');

        $this->flysystem = new Filesystem(
            new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket));

        $this->flysystem->addPlugin(new AWS_S3_Plugin\PresignedUrl());
    }

    public function __call($method, $args)
    {
        return $this->flysystem->$method(...$args);
    }
}

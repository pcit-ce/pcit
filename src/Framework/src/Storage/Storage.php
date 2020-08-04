<?php

declare(strict_types=1);

namespace PCIT\Framework\Storage;

use Etime\Flysystem\Plugin\AWS_S3 as AWS_S3_Plugin;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class Storage
{
    public $flysystem;

    public function __construct()
    {
        $adapter = $this->getAdapter();

        $this->flysystem = new Filesystem($adapter);

        $this->addPlugin();
    }

    public function __call($method, $args)
    {
        return $this->flysystem->$method(...$args);
    }

    public function getAdapter(): AdapterInterface
    {
        $disk = config('filesystems.default');

        $localAdapter = new LocalAdapter('/tmp');

        if ('local' === $disk) {
            return $localAdapter;
        } elseif ('s3' === $disk) {
            $bucket = config('filesystems.bucket');

            $options = config('filesystems.disks.s3');

            $s3Adapter = new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket);

            return $s3Adapter;
        }

        return $localAdapter;
    }

    public function addPlugin(): void
    {
        $disk = config('filesystems.default');

        if ('s3' === $disk) {
            $this->flysystem->addPlugin(new AWS_S3_Plugin\PresignedUrl());
        }
    }
}

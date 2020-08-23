<?php

declare(strict_types=1);

namespace PCIT\Framework\Storage;

use Etime\Flysystem\Plugin\AWS_S3 as AWS_S3_Plugin;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class Storage
{
    /** @var array */
    public $filesystem;

    public function disk(?string $name = null): FilesystemInterface
    {
        $name = $name ?: config('filesystems.default');

        if ($filesystem = $this->filesystem[$name] ?? false) {
            return $filesystem;
        }

        if ('local' === $name) {
            $adapter = new LocalAdapter('/tmp');
        } elseif ('s3' === $name) {
            $bucket = config('filesystems.bucket');

            $options = config('filesystems.disks.s3');

            $adapter = new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket);
        }

        $filesystem = new Filesystem($adapter);

        // 添加插件
        if ('s3' === $name) {
            $filesystem->addPlugin(new AWS_S3_Plugin\PresignedUrl());
        }

        return $this->filesystem[$name] = $filesystem;
    }

    public function __call(string $method, array $args)
    {
        return $this->disk()->$method(...$args);
    }
}

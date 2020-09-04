<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PCIT\Framework\Attributes\Route;

class CachesController
{
    public $flysystem;

    public function __construct()
    {
        $bucket = config('filesystems.cache_bucket');

        $options = config('filesystems.disks.s3');

        $this->flysystem = new Filesystem(
            new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket)
        );

        // $this->flysystem->addPlugin(new AWS_S3_Plugin\PresignedUrl());
    }

    /**
     * Returns all the caches for a repository.
     *
     * @param array $args
     *
     * @throws \Exception
     */
    #[Route('get', 'api/repo/{username}/{repo_name}/caches')]
    public function __invoke(...$args)
    {
        list($username, $repo_name) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $path = "$git_type/$rid";

        $result = $this->flysystem->listContents($path, true);

        $result2 = [];

        foreach ($result as $item) {
            if ('dir' === $item['type']) {
                continue;
            }

            $branch = explode('/', $item['dirname'])[2];

            $size = $result2[$branch]['size'] ?? 0;
            $result2[$branch]['size'] = $size + $item['size'] / 1024 / 1024;
        }

        return $result2;
    }

    /**
     * Deletes caches for a repository.
     *
     * @param array $args
     *
     * @throws \Exception
     */
    #[Route('delete', 'api/repo/{username}/{repo_name}/caches')]
    #[Route('delete', 'api/repo/{username}/{repo_name}/caches/{branch}')]
    public function delete(...$args)
    {
        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);
        $path = "$git_type/$rid";

        if (2 == \count($args)) {
            list($username, $repo_name) = $args;
        } else {
            list($username, $repo_name, $branch) = $args;
            $path .= "/$branch";
        }

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $this->flysystem->deleteDir($path);

        return \Response::make('', 204);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use App\Job;
use Etime\Flysystem\Plugin\AWS_S3 as AWS_S3_Plugin;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class Artifact
{
    public $flysystem;

    public function __construct()
    {
        $bucket = config('filesystems.artifact_bucket');

        $options = config('filesystems.disks.s3');

        $this->flysystem = new Filesystem(
            new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket)
        );

        $this->flysystem->addPlugin(new AWS_S3_Plugin\PresignedUrl());
    }

    @@\Route('get', 'api/{git_type}/{username}/{repo_name}/artifacts')
    public function listByRepo(...$args)
    {
        [$git_type,$user,$repo] = $args;

        // pcit-artifact/$git_type/$user/$repo

        $path = "$git_type/$user/$repo";

        return $this->flysystem->listContents($path, true);
    }

    @@\Route('get', 'api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts')
    public function listByJob(...$args)
    {
        [$git_type,$user,$repo,$job_id] = $args;

        // pcit-artifact/$user/$repo/$job_id

        $path = "$git_type/$user/$repo/$job_id";

        return $this->flysystem->listContents($path);
    }

    @@\Route('get', 'api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts/{file_name}')
    public function __invoke(...$args)
    {
        [$git_type,$user,$repo,$job_id,$file_name] = $args;

        $path = "$git_type/$user/$repo/$job_id/$file_name";

        return $this->flysystem->getMetadata($path);
    }

    @@\Route('get', 'api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts/{file_name}/{format}')
    public function download(...$args)
    {
        [$git_type,$user,$repo,$job_id,$file_name,$format] = $args;

        $path = "$git_type/$user/$repo/$job_id/$file_name";

        try {
            $this->flysystem->assertPresent($path);

            $url = $this->flysystem->getPresignedUrl($path, '+60 minutes');

            return \Response::redirect($url);
        } catch (\Exception $e) {
            return \Response::make('not found', 404);
        }
    }

    @@\Route('delete', 'api/{git_type}/{username}/{repo_name}/jobs/{job_id}/artifacts/{file_name}')
    public function delete(...$args)
    {
        [$git_type,$user,$repo,$job_id,$file_name] = $args;

        JWTController::check(Job::getBuildKeyId((int) $job_id));

        $path = "$git_type/$user/$repo/$job_id/$file_name";

        try {
            $this->flysystem->delete($path);
        } catch (\Exception $e) {
        }

        return \Response::make('', 204);
    }
}

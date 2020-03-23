<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use Etime\Flysystem\Plugin\AWS_S3 as AWS_S3_Plugin;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class Artifact
{
    public $flysystem;

    public function __construct()
    {
        $bucket = env('CI_S3_ARTIFACT_BUCKET', 'pcit-artifact');

        $options = [
            'version' => 'latest',
            'region' => env('CI_S3_REGION', 'us-east'),
            'endpoint' => env('CI_S3_ENDPOINT'),
            'use_path_style_endpoint' => env('CI_S3_USE_PATH_STYLE_ENDPOINT', true),
            'credentials' => [
                'key' => env('CI_S3_ACCESS_KEY_ID'),
                'secret' => env('CI_S3_SECRET_ACCESS_KEY'),
            ],
            'http' => [
                'connect_timeout' => 0,
            ],
        ];

        $this->flysystem = new Filesystem(
            new AwsS3Adapter(new \Aws\S3\S3Client($options), $bucket));

        $this->flysystem->addPlugin(new AWS_S3_Plugin\PresignedUrl());
    }

    public function listByRepo(...$args)
    {
        [$git_type,$user,$repo] = $args;

        // pcit-artifact/$git_type/$user/$repo

        $path = "$git_type/$user/$repo";

        return $this->flysystem->listContents($path, true);
    }

    public function listByJob(...$args)
    {
        [$git_type,$user,$repo,$job_id] = $args;

        // pcit-artifact/$user/$repo/$job_id

        $path = "$git_type/$user/$repo/$job_id";

        return $this->flysystem->listContents($path);
    }

    public function __invoke(...$args)
    {
        [$git_type,$user,$repo,$job_id,$file_name] = $args;

        $path = "$git_type/$user/$repo/$job_id/$file_name";

        return $this->flysystem->getMetadata($path);
    }

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

    public function delete(...$args)
    {
        [$git_type,$user,$repo,$job_id,$file_name] = $args;

        $path = "$git_type/$user/$repo/$job_id/$file_name";

        try {
            $this->flysystem->delete($path);
        } catch (\Exception $e) {
        }

        return \Response::make('', 204);
    }
}

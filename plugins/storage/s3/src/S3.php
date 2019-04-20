<?php

declare(strict_types=1);

namespace PCIT\Plugin\Adapter;

class S3 extends AbstractAdapter
{
    const PROVIDER = 's3';

    public $region = 'us-east-1';
    public $access_key_id;
    public $secret_access_key;
    public $bucket = 'pcit';
    public $acl = 'public_read';
    public $local_dir = 'public';
    public $upload_dir;
    public $endpoint;
    public $minio = false;

    public function __construct(array $config)
    {
        $this->region = $config['region'] ?? 'us-east-1';
        $this->access_key_id = $config['access_key_id'] ?? null;
        $this->secret_access_key = $config['secret_access_key'] ?? null;
        $this->bucket = $config['bucket'] ?? 'pcit';
        $this->acl = $config['acl'] ?? 'public_read';
        $this->local_dir = $config['local_dir'] ?? 'public';
        $this->upload_dir = $config['upload_dir'] ?? $this->local_dir;
        $this->endpoint = $config['endpoint'] ?? null;
        $this->minio = $config['minio'] ?? false;
    }

    public function deploy(): array
    {
        return [
            'image' => 'pcit/s3',
            'env' => [
                'PCIT_S3_REGION='.$this->region,
                'PCIT_S3_ACCESS_KEY_ID='.$this->access_key_id,
                'PCIT_S3_SECRET_ACCESS_KEY='.$this->secret_access_key,
                'PCIT_S3_BUCKET='.$this->bucket,
                'PCIT_S3_ACL='.$this->acl,
                'PCIT_S3_LOCAL_DIR='.$this->local_dir,
                'PCIT_S3_UPLOAD_DIR='.$this->upload_dir,
                'PCIT_S3_ENDPOINT='.$this->endpoint,
                'PCIT_S3_USE_PATH_STYLE_ENDPOINT='.($this->minio ? 'true' : 'false'),
            ],
        ];
    }
}

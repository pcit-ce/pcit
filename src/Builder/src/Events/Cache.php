<?php

declare(strict_types=1);

namespace PCIT\Builder\Events;

use Docker\Container\Client as DockerContainer;
use PCIT\PCIT;
use PCIT\Support\CacheKey;
use PCIT\Support\Env;

class Cache
{
    public $build_key_id;

    public $jobId;

    public $cache;

    public $workdir;

    public $gitType;

    public $rid;

    public $branch;

    /**
     * Cache constructor.
     *
     * @param int    $jobId
     * @param int    $build_key_id
     * @param string $workdir
     * @param mixed  $cache
     */
    public function __construct(int $jobId,
                                int $build_key_id,
                                string $workdir,
                                string $gitType,
                                int $rid,
                                string $branch,
                                $cache = null,
                                $disableUpload = false)
    {
        $this->jobId = $jobId;
        $this->build_key_id = $build_key_id;
        $this->workdir = $workdir;
        $this->gitType = $gitType;
        $this->rid = $rid;
        $this->branch = $branch;
        $this->cache = $cache;
        $this->disableUpload = $disableUpload;
    }

    /**
     * @throws \Exception
     */
    public function getPrefix()
    {
        // github_rid_branch_folder
        $prefix = sprintf('%s_%s_%s', $this->gitType, $this->rid, $this->branch);

        return $prefix;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        if (!$this->cache) {
            return;
        }

        $array = $this->cache->directories ?? [];

        if (!\is_array($array)) {
            return;
        }

        $dockerContainer = app(PCIT::class)->docker->container;

        $prefix = $this->getPrefix();

        $env = [
            'S3_ENDPOINT='.env('CI_S3_ENDPOINT'),
            'S3_ACCESSKEYID='.env('CI_S3_ACCESSKEYID'),
            'S3_SECRETACCESSKEY='.env('CI_S3_SECRETACCESSKEY'),
            'S3_BUCKET='.env('', 'pcit'),
            'S3_REGION='.env('', 'us-east-1'),
            'S3_CACHE_PREFIX='.$prefix,
            'S3_CACHE='.json_encode($this->cache->directories),
            'S3_CACHE_DOWNLOAD=true',
        ];

        \PCIT\Support\Cache::store()
            ->set(CacheKey::cacheKey($this->jobId, 'download'),
                $this->getContainerConfig($dockerContainer, $env)
            );

        array_pop($env);

        if ($this->disableUpload) {
            return;
        }

        \PCIT\Support\Cache::store()
            ->set(CacheKey::cacheKey($this->jobId, 'upload'),
                $this->getContainerConfig($dockerContainer, $env)
            );
    }

    /**
     * @param DockerContainer $dockerContainer
     * @param                 $env
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function getContainerConfig(DockerContainer $dockerContainer, $env)
    {
        return $config = $dockerContainer
            ->setImage('pcit/s3')
            ->setEnv($env)
            ->setWorkingDir($this->workdir)
            ->setLabels([
                'com.khs1994.ci' => (string) $this->jobId,
            ])
            ->setBinds(["pcit_$this->jobId:$this->workdir"])
            ->setCreateJson(null)
            ->getCreateJson();
    }
}

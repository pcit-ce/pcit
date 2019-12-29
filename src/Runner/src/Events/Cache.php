<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use Docker\Container\Client as DockerContainer;
use PCIT\Framework\Support\Env;
use PCIT\PCIT;
use PCIT\Support\CacheKey;

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
     * @param mixed $cache
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

        $cacheList = $cachesDir = $this->cache ?? [];

        $cacheList = \is_string($cacheList) ? [$cacheList] : $cacheList;

        if (!\is_array($cacheList)) {
            return;
        }

        $dockerContainer = app(PCIT::class)->docker->container;

        $prefix = $this->getPrefix();

        $env = [
            'INPUT_ENDPOINT='.env('CI_S3_ENDPOINT'),
            'INPUT_ACCESS_KEY_ID='.env('CI_S3_ACCESS_KEY_ID'),
            'INPUT_SECRET_ACCESS_KEY='.env('CI_S3_SECRET_ACCESS_KEY'),
            'INPUT_BUCKET='.env('', 'pcit'),
            'INPUT_REGION='.env('', 'us-east-1'),
            'INPUT_CACHE_PREFIX='.$prefix,
            'INPUT_CACHE='.(new EnvHandler())->arrayHandler($cacheList),
            'INPUT_USE_PATH_STYLE_ENDPOINT='.
            (env('CI_S3_USE_PATH_STYLE_ENDPOINT', true) ? 'true' : 'false'),
            // must latest key
            'INPUT_CACHE_DOWNLOAD=true',
        ];

        \PCIT\Framework\Support\Cache::store()
            ->set(CacheKey::cacheKey($this->jobId, 'download'),
                $this->getContainerConfig($dockerContainer, $env)
            );

        array_pop($env);

        if ($this->disableUpload) {
            return;
        }

        \PCIT\Framework\Support\Cache::store()
            ->set(CacheKey::cacheKey($this->jobId, 'upload'),
                $this->getContainerConfig($dockerContainer, $env)
            );
    }

    /**
     * @param $env
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

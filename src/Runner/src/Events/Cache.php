<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use Docker\Container\Client as DockerContainer;
use PCIT\PCIT;
use PCIT\Runner\Events\Handler\EnvHandler;
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

    public $disableUpload;

    /**
     * 一个 job 一个缓存.
     *
     * @var array|null
     */
    public $matrix;

    /**
     * Cache constructor.
     *
     * @param string|array $cacheConfig
     */
    public function __construct(int $jobId,
                                int $build_key_id,
                                string $workdir,
                                string $gitType,
                                int $rid,
                                string $branch,
                                ?array $matrix,
                                $cacheConfig = null,
                                bool $disableUpload = false)
    {
        $this->jobId = $jobId;
        $this->build_key_id = $build_key_id;
        $this->workdir = $workdir;
        $this->gitType = $gitType;
        $this->rid = $rid;
        $this->branch = $branch;
        $this->matrix = $matrix;
        $this->cache = $cacheConfig;
        $this->disableUpload = $disableUpload;
    }

    /**
     * @throws \Exception
     */
    public function getPrefix(): string
    {
        $matrix = $this->matrix ?? [];
        ksort($matrix);

        $matrix = md5(json_encode($matrix));

        // {git_type}_{rid}_{branch}-{matrix}
        $prefix = sprintf('%s_%s_%s-%s', $this->gitType, $this->rid, $this->branch, $matrix);

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
            'INPUT_BUCKET='.env('CI_S3_BUCKET', 'pcit'),
            'INPUT_REGION='.env('CI_S3_REGION', 'us-east-1'),
            'INPUT_CACHE_PREFIX='.$prefix,
            'INPUT_CACHE='.(new EnvHandler())->array2str($cacheList),
            'INPUT_USE_PATH_STYLE_ENDPOINT='.
            (env('CI_S3_USE_PATH_STYLE_ENDPOINT', true) ? 'true' : 'false'),
            // must latest key
            'INPUT_CACHE_DOWNLOAD=true',
        ];

        $container_config = $this->getContainerConfig($dockerContainer, $env);

        \Log::info('🔽Handle cache downloader', json_decode($container_config, true));

        \Cache::store()
            ->set(CacheKey::cacheKey($this->jobId, 'download'), $container_config);

        array_pop($env);

        if ($this->disableUpload) {
            return;
        }

        $container_config = $this->getContainerConfig($dockerContainer, $env);

        \Log::info('🔼Handle cache uploader', json_decode($container_config, true));

        \Cache::store()
            ->set(CacheKey::cacheKey($this->jobId, 'upload'), $container_config);
    }

    private function getContainerConfig(DockerContainer $dockerContainer, ?array $env): string
    {
        return $dockerContainer
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

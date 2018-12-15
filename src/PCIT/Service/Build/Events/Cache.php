<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Events;

use App\Build;
use Docker\Container\Client;
use PCIT\PCIT as PCIT;
use PCIT\Support\Env;

class Cache
{
    public $build_key_id;

    public $job_id;

    public $cache;

    public $workdir;

    /**
     * Cache constructor.
     *
     * @param int    $job_id
     * @param int    $build_key_id
     * @param string $workdir
     * @param mixed  $cache
     */
    public function __construct(int $job_id, int $build_key_id, string $workdir, $cache = null)
    {
        $this->job_id = $job_id;

        $this->build_key_id = $build_key_id;

        $this->cache = $cache;

        $this->workdir = $workdir;
    }

    /**
     * @throws \Exception
     */
    public function getPrefix()
    {
        $git_type = Build::getGitType($this->build_key_id);
        $rid = Build::getRid($this->build_key_id);
        $branch = Build::getBranch($this->build_key_id);

        // github_rid_branch_folder
        $prefix = sprintf('%s_%s_%s', $git_type, $rid, $branch);

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

        $docker_container = (new PCIT())->docker->container;

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
            ->set('pcit/'.$this->job_id.'/cache_download',
                $this->getContainerConfig($docker_container, $env)
            );

        array_pop($env);

        \PCIT\Support\Cache::store()
            ->set('pcit/'.$this->job_id.'/cache_upload',
                $this->getContainerConfig($docker_container, $env)
            );
    }

    /**
     * @param Client $docker_container
     * @param        $env
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function getContainerConfig(Client $docker_container, $env)
    {
        return $config = $docker_container
            ->setImage('khs1994/s3')
            ->setEnv($env)
            ->setWorkingDir($this->workdir)
            ->setLabels([
                'com.khs1994.ci' => (string) $this->job_id,
            ])
            ->setBinds(["$this->job_id:$this->workdir"])
            ->setCreateJson(null)
            ->getCreateJson();
    }
}

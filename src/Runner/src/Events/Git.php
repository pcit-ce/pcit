<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\GPI\Support\Git as GitSupport;
use PCIT\PCIT;
use PCIT\Runner\BuildData;
use PCIT\Runner\Events\Handler\EnvHandler;
use PCIT\Runner\Events\Handler\TextHandler;
use PCIT\Runner\JobGenerator;
use PCIT\Runner\RPC\Cache;
use PCIT\Support\CacheKey;
use PCIT\Support\CI;

class Git
{
    private $git;

    private $build;

    private $jobGenerator;

    public function __construct($git, ?BuildData $build, ?JobGenerator $jobGenerator)
    {
        $this->git = $git;
        $this->build = $build;
        $this->jobGenerator = $jobGenerator;
    }

    public function parseGit(): array
    {
        $envHandler = new EnvHandler();
        $textHandler = new TextHandler();

        $git = $this->git;

        $env = array_merge(
            $this->jobGenerator->system_env ?? [],
            $this->jobGenerator->system_job_env ?? []
        );

        $hosts = $git->hosts ?? [];
        $hosts = array_merge($hosts, $this->jobGenerator->networks->hosts ?? []);
        $hosts = $textHandler->handleArray($hosts, $env);

        $git_image = $git->image ?? 'pcit/git';
        $git_image = $textHandler->handle($git_image, $env);

        unset($git->image, $git->hosts);

        $git_config = $envHandler->handle(
            (array) $git,
            $env,
            'PLUGIN',
            true
        );

        return [$git_config, $git_image, $hosts];
    }

    /**
     * @see https://github.com/drone-plugins/drone-git
     */
    public function handle(): void
    {
        $git = $this->git;

        if ($this->git->disable ?? false) {
            \Log::emergency('🛑git clone disabled');

            return;
        }

        $jobGenerator = $this->jobGenerator;
        $build = $this->build;

        $git_image = 'pcit/git';
        $git_config = [];
        $hosts = [];
        $git_env = null;

        if ($git) {
            list($git_config, $git_image, $hosts) = self::parseGit();
        } else {
            $git_config[] = 'PLUGIN_DEPTH=25';
        }

        if (env('CI_GITHUB_HOST')) {
            $hosts = array_merge(
                $hosts,
                ['github.com:'.env('CI_GITHUB_HOST')]
            );
        }

        $git_url = GitSupport::getUrl($build->git_type, $build->repo_full_name);

        if ($github_mirror = env('CI_GITHUB_MIRROR')) {
            $git_url = str_replace('github.com', $github_mirror, $git_url);
        }

        switch ($build->event_type) {
            case CI::BUILD_EVENT_PUSH || CI::BUILD_EVENT_MANUALLY:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$jobGenerator->workdir,
                    'DRONE_BUILD_EVENT=push',
                    'DRONE_COMMIT_SHA='.$build->commit_id,
                    'DRONE_COMMIT_REF='.'refs/heads/'.$build->branch,
                ], $git_config);

                break;
            case CI::BUILD_EVENT_PR:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$jobGenerator->workdir,
                    'DRONE_BUILD_EVENT=pull_request',
                    'DRONE_COMMIT_SHA='.$build->commit_id,
                    'DRONE_COMMIT_REF=refs/pull/'.$jobGenerator->build->pull_request_number.'/head',
                ], $git_config);

                break;
            case  CI::BUILD_EVENT_TAG:
                $git_env = array_merge([
                    'DRONE_REMOTE_URL='.$git_url,
                    'DRONE_WORKSPACE='.$jobGenerator->workdir,
                    'DRONE_BUILD_EVENT=tag',
                    'DRONE_COMMIT_SHA='.$build->commit_id,
                    'DRONE_COMMIT_REF=refs/tags/'.$jobGenerator->build->tag,
                ], $git_config);

                break;
        }

        $config = $this->generateDocker($git_env, $git_image, $hosts, (int) $jobGenerator->job_id, $jobGenerator->workdir);

        $this->storeContainerConfig($config, (int) $jobGenerator->job_id);
    }

    public function storeContainerConfig(string $config, int $job_id): void
    {
        \Log::info('📥Handle clone git', json_decode($config, true));

        Cache::set(CacheKey::cloneKey($job_id), $config);
    }

    public function generateDocker(
        ?array $git_env,
        string $git_image,
        ?array $hosts,
        int $job_id,
        string $workdir,
        array $binds = []
    ): string {
        /**
         * @var \Docker\Container\Client
         */
        $docker_container = app(PCIT::class)->docker->container;

        if (!$binds) {
            $binds = ["pcit_$job_id:$workdir"];
        }

        return $docker_container
            ->setEnv($git_env)
            ->setLabels([
                'com.khs1994.ci.git' => (string) $job_id,
                'com.khs1994.ci' => (string) $job_id,
            ])
            ->setBinds($binds)
            ->setExtraHosts($hosts ?? [])
            ->setImage($git_image)
            ->setWorkingDir($workdir)
            ->setCreateJson(null)
            ->getCreateJson();
    }
}

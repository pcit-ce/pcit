<?php

declare(strict_types=1);

namespace PCIT\Runner;

class SystemEnv
{
    public $build;

    public $client;

    /**
     * @var array<string> ['k=v']
     */
    public $env;

    public function __construct(BuildData $build, Client $client)
    {
        $this->build = $build;
        $this->client = $client;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $ci_host = env('CI_HOST');
        $gitType = $this->build->git_type;
        $repo_full_name = $this->build->repo_full_name;
        $build_id = $this->build->build_key_id;

        $system_env = [
            'CI=true',
            'PCIT=true',
            'CONTINUOUS_INTEGRATION=true',
            'PCIT_BRANCH='.$this->build->branch,
            'PCIT_TAG='.$this->build->tag,
            'PCIT_BUILD_DIR='.$this->client->workdir,
            'PCIT_BUILD_ID='.$build_id,
            "PCIT_BUILD_WEB_URL=${ci_host}/$gitType/$repo_full_name/builds/$build_id",
            'PCIT_COMMIT='.$this->build->commit_id,
            'PCIT_COMMIT_MESSAGE='.$this->build->commit_message,
            'PCIT_EVENT_TYPE='.$this->build->event_type,
            'PCIT_PULL_REQUEST=false',
            'PCIT_REPO_SLUG='.$repo_full_name,
            'PCIT_REPO='.$repo_full_name,

            'DEBIAN_FRONTEND=noninteractive',
            'LANG=en_US.UTF-8',
            'LC_ALL=en_US.UTF-8',

            // 'PCIT_REF'
        ];

        if ($this->build->pull_request_number) {
            array_merge($system_env,
                [
                    'PCIT_PULL_REQUEST=true',
                    'PCIT_PULL_REQUEST_BRANCH='.$this->build->branch,
                    'PCIT_PULL_REQUEST_SHA='.$this->build->commit_id,
                    'PCIT_PULL_REQUEST_INTERNAL='.$this->build->internal,
                ]
            );
        }

        $system_env = array_merge($system_env, $this->client->system_env);

        \Log::emergency('📐generate system env', $system_env);

        $this->env = $system_env;

        return $this;
    }
}

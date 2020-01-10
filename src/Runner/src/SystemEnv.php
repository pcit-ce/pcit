<?php

declare(strict_types=1);

namespace PCIT\Runner;

class SystemEnv
{
    public $build;

    public $client;

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
        $system_env = [
            'CI=true',
            'PCIT=true',
            'CONTINUOUS_INTEGRATION=true',
            'PCIT_BRANCH='.$this->build->branch,
            'PCIT_TAG='.$this->build->tag,
            'PCIT_BUILD_DIR='.$this->client->workdir,
            'PCIT_BUILD_ID='.$this->build->build_key_id,
            'PCIT_COMMIT='.$this->build->commit_id,
            'PCIT_COMMIT_MESSAGE='.$this->build->commit_message,
            'PCIT_EVENT_TYPE='.$this->build->event_type,
            'PCIT_PULL_REQUEST=false',
            'PCIT_REPO_SLUG='.$this->build->repo_full_name,

            // 'PCIT_REF'
        ];

        if ($this->build->pull_request_number) {
            array_merge($system_env,
                [
                    'PCIT_PULL_REQUEST=true',
                    'PCIT_PULL_REQUEST_BRANCH='.$this->build->branch,
                    'PCIT_PULL_REQUEST_SHA='.$this->build->commit_id,
                ]
            );
        }

        $system_env = array_merge($system_env, $this->client->system_env);

        \Log::emergency(json_encode($system_env), []);

        $this->env = $system_env;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use KhsCI\Support\Log;

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
            'KHSCI=true',
            'CONTINUOUS_INTEGRATION=true',
            'KHSCI_BRANCH='.$this->build->branch,
            'KHSCI_TAG='.$this->build->tag,
            'KHSCI_BUILD_DIR='.$this->client->workdir,
            'KHSCI_BUILD_ID='.$this->build->build_key_id,
            'KHSCI_COMMIT='.$this->build->commit_id,
            'KHSCI_COMMIT_MESSAGE='.$this->build->commit_message,
            'KHSCI_EVENT_TYPE='.$this->build->event_type,
            'KHSCI_PULL_REQUEST=false',
            'KHSCI_REPO_SLUG='.$this->build->repo_full_name,
        ];

        if ($this->build->pull_request_number) {
            array_merge($system_env,
                [
                    'KHSCI_PULL_REQUEST=true',
                    'KHSCI_PULL_REQUEST_BRANCH='.$this->build->branch,
                    'KHSCI_PULL_REQUEST_SHA='.$this->build->commit_id,
                ]
            );
        }

        $system_env = array_merge($system_env, $this->client->system_env);

        Log::debug(__FILE__, __LINE__, json_encode($system_env), [], Log::EMERGENCY);

        $this->env = $system_env;

        return $this;
    }
}

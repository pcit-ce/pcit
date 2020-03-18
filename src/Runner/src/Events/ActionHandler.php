<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\Framework\Support\HttpClient;
use Symfony\Component\Yaml\Yaml;

class ActionHandler
{
    private $step;

    public function __construct(Pipeline $step)
    {
        $this->step = $step;
    }

    /**
     * return action commands.
     */
    public function handle(string $step, string $image)
    {
        // github://
        $actions = substr($image, 9);

        // user/repo@ref
        // user/repo/path@ref
        $explode_array = explode('@', $actions);
        [$repo,] = $explode_array;

        $ref = 'master';
        if ($explode_array[1] ?? false) {
            $ref = $explode_array[1];
        }

        $explode_array = explode('/', $repo, 3);

        [$user,$repo] = $explode_array;
        $repo = $user.'/'.$repo;

        $path = null;
        if ($explode_array[2] ?? false) {
            $path = '/'.$explode_array[2];
        }

        \Log::info('this pipeline use actions', [
          'repo' => $repo,
          'path' => $path,
          'ref' => $ref,
        ]);

        // git clone
        $workdir = '/var/run/actions/'.$repo;
        $this->handleGit($step, $repo, $ref);

        // action.yml
        $action_yml = HttpClient::get(
            'https://raw.githubusercontent.com/'.$repo.'/'.$ref.$path.'/action.yml',
            null,
            [],
            20
        );

        $action_yml = Yaml::parse($action_yml);

        $using = $action_yml['runs']['using'];
        $main = $action_yml['runs']['main'] ?? 'index.js';
        $main = $workdir.$path.'/'.$main;

        if ('node' === substr($using, 0, 4)) {
            $using = 'node';
        }

        return [
          "$using $main",
      ];
    }

    public function handleGit($step, $repo, $ref): void
    {
        $step .= '_actions_downloader';
        $workdir = '/var/run/actions/'.$repo;
        $jobId = $this->step->client->job_id;
        $env = [
            'INPUT_REPO='.$repo,
            'INPUT_REF='.$ref,
        ];

        $config = (new Git(null, null, null))->generateDocker(
            $env,
            'pcit/actions-downloader',
            [],
            $jobId,
            $workdir,
            [
                'pcit_actions_'.$jobId.':'.'/var/run/actions',
            ]
        );

        $this->step->storeCache($jobId, $step, $config);
    }

    public function handleEnv($step, $workdir)
    {
        return [
        'GITHUB_WORKSPACE='.$workdir,
        'RUNNER_WORKSPACE'.$workdir,
        'GITHUB_REF=',
        'GITHUB_SHA='.$this->step->build->commit_id,
        'RUNNER_OS=Linux',
        'RUNNER_USER=',
        'RUNNER_TEMP=/home/runner/work/_temp',
        'GITHUB_REPOSITORY='.$this->step->build->repo_full_name,
        'GITHUB_EVENT_NAME='.$this->step->build->event_type,
        'GITHUB_WORKFLOW='.$step,
        'GITHUB_ACTIONS=true',
        'GITHUB_HEAD_REF=',
        'GITHUB_BASE_REF=',
        'GITHUB_ACTOR=',
        'GITHUB_ACTION=run9',
        'GITHUB_EVENT_PATH=/home/runner/work/_temp/_github_workflow/event.json',
      ];
    }
}

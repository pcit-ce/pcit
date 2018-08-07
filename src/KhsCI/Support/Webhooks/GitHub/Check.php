<?php

declare(strict_types=1);

namespace KhsCI\Support\Webhooks\GitHub;

use KhsCI\Support\Log;

class Check
{
    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function suite($json_content)
    {
        $obj = json_decode($json_content);

        $installation_id = $obj->installation->id ?? null;
        $repository = $obj->repository;
        $rid = $repository->id;
        $repo_full_name = $repository->full_name;
        $username = $repository->owner->login;
        $action = $obj->action;

        Log::debug(null, null, 'Receive event', ['Check Suite' => $action], Log::INFO);

        $check_suite = $obj->check_suite;
        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;
        $commit_id = $check_suite->head_sha;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'action' => $action,
            'branch' => $branch,
            'commit_id' => $commit_id,
            'check_suite_id' => $check_suite_id,
            'username' => $username,
        ];
    }

    /**
     * @param $json_content
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function run($json_content)
    {
        $obj = json_decode($json_content);

        $action = $obj->action;

        Log::debug(null, null, 'Receive event', ['Check Run' => $action], Log::INFO);

        $installation_id = $obj->installation->id ?? null;

        $repository = $obj->repository;

        $rid = $repository->id;

        $repo_full_name = $repository->full_name;

        $username = $repository->owner->login;

        $check_run = $obj->check_run;
        $check_suite = $check_run->check_suite;

        $check_run_id = $check_run->id;
        $commit_id = $check_run->head_sha;
        $external_id = $check_run->external_id;
        $check_suite = $check_run->check_suite;

        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'action' => $action,
            'branch' => $branch,
            'username' => $username,
            'check_run_id' => $check_run_id,
            'commit_id' => $commit_id,
            'external_id' => $external_id,
            'check_suite_id' => $check_suite_id,
        ];
    }
}

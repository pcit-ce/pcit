<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parse;

use PCIT\Framework\Support\Log;
use PCIT\GitHub\Webhooks\Parse\UserBasicInfo\Account;

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

        $action = $obj->action;

        Log::debug(null, null, 'Receive event', ['Check Suite' => $action], Log::INFO);

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $check_suite = $obj->check_suite;
        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;
        $commit_id = $check_suite->head_sha;

        $org = $obj->organization ? true : false;

        return [
            'installation_id' => $installation_id,
            'rid' => $rid,
            'repo_full_name' => $repo_full_name,
            'action' => $action,
            'branch' => $branch,
            'commit_id' => $commit_id,
            'check_suite_id' => $check_suite_id,
            'account' => (new Account($repository_owner, $org)),
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

        // 仓库所属用户或组织的信息
        $repository_owner = $repository->owner;

        $check_run = $obj->check_run;

        $check_run_id = $check_run->id;
        $commit_id = $check_run->head_sha;
        $external_id = $check_run->external_id;
        $check_suite = $check_run->check_suite;

        $check_suite_id = $check_suite->id;
        $branch = $check_suite->head_branch;

        $org = ($obj->organization ?? false) ? true : false;

        $account = (new Account($repository_owner, $org));

        return compact('installation_id', 'rid', 'repo_full_name', 'action',
            'branch', 'commit_id', 'check_suite_id', 'check_run_id', 'external_id', 'account');
    }
}

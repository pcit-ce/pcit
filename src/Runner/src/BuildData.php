<?php

declare(strict_types=1);

namespace PCIT\Runner;

abstract class BuildData
{
    public $commit_id;

    public $commit_message;

    public $unique_id;

    public $event_type;

    public $build_key_id;

    public $pull_request_number;

    public $tag;

    /**
     * @var int
     */
    public $rid;

    public $repo_full_name;

    /**
     * @var string
     */
    public $git_type;

    public $config;

    public $build_status;

    public $description;

    public $branch;

    /**
     * env add by settings.
     *
     * @var array<string> ['k=v']
     */
    public $env;

    /**
     * 是否为内部 pr.
     *
     * @var 0|1
     */
    public $internal;

    // repo config

    public $build_pushes;

    public $build_pull_requests;

    public $maximum_number_of_builds;

    public $auto_cancel_branch_builds;

    public $auto_cancel_pull_request_builds;
}

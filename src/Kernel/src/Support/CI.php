<?php

declare(strict_types=1);

namespace PCIT\Support;

class CI
{
    public const CONFIG_MISCONFIGURED = 'misconfigured';

    public const BUILD_ACTIVATE = 1;

    public const BUILD_DEACTIVATE = 0;

    public const BUILD_EVENT_PUSH = 'push';

    public const BUILD_EVENT_REPOSITORY_DISPATCH = 'repository_dispatch';

    public const BUILD_EVENT_TAG = 'tag';

    public const BUILD_EVENT_PR = 'pull_request';

    public const BUILD_EVENT_ISSUE = 'issue';

    public const GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS = 'success'; // web-passed ico-passing

    public const GITHUB_CHECK_SUITE_CONCLUSION_FAILURE = 'failure'; // web-failed ico-failing

    // 中性的.
    public const GITHUB_CHECK_SUITE_CONCLUSION_NEUTRAL = 'neutral';

    public const GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED = 'cancelled'; // web-canceled web-errored ico-error

    public const GITHUB_CHECK_SUITE_CONCLUSION_SKIPPED = 'skipped';

    public const GITHUB_CHECK_SUITE_CONCLUSION_TIMED_OUT = 'timed_out';

    // 需要注意，有意外情况.
    public const GITHUB_CHECK_SUITE_CONCLUSION_ACTION_REQUIRED = 'action_required';

    // status
    // 1. 将 webhooks 转化为 build，build 状态变为 pending
    // 2. 轮询 pending build，生成 job，
    // build 状态变为 queued，异常则变为 cancelled
    // job 状态变为 queued

    // 1. agent 轮询 queued job
    // 2. job 状态变为 in_progress
    // 3. 根据运行结果，变更 job 状态
    // 4. 根据 job 状态，同步 build 状态
    public const GITHUB_CHECK_SUITE_STATUS_QUEUED = 'queued'; // pending web-created web-started

    public const GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS = 'in_progress';

    public const GITHUB_CHECK_SUITE_STATUS_COMPLETED = 'completed';

    public const MEDIA_TYPE_COMMENT_BODY_RAW = 'application/vnd.github.v3.raw+json';

    public const MEDIA_TYPE_COMMENT_BODY_TEXT = 'application/vnd.github.v3.text+json';

    public const MEDIA_TYPE_COMMENT_BODY_HTML = 'application/vnd.github.v3.html+json';

    public const MEDIA_TYPE_COMMENT_BODY_FULL = 'application/vnd.github.v3.full+json';

    public const MEDIA_TYPE_GIT_BLOB_JSON = 'application/vnd.github.v3+json';

    public const MEDIA_TYPE_GIT_BLOB_RAW = 'application/vnd.github.v3.raw';

    public const MEDIA_TYPE_COMMITS_DIFF = 'application/vnd.github.v3.diff';

    public const MEDIA_TYPE_COMMITS_PATCH = 'application/vnd.github.v3.patch';

    public const MEDIA_TYPE_COMMITS_SHA = 'application/vnd.github.v3.sha';

    public const MEDIA_TYPE_REPOSITORY_CONTENTS_RAW = 'application/vnd.github.v3.raw';

    public const MEDIA_TYPE_REPOSITORY_CONTENTS_HTML = 'application/vnd.github.v3.html';

    public const MEDIA_TYPE_GIST_RAW = 'application/vnd.github.v3.raw';

    public const MEDIA_TYPE_GIST_BASE64 = 'application/vnd.github.v3.base64';

    public const CI_SETTING_ARRAY = [
        'build_pushes',
        'build_pull_requests',
        'maximum_number_of_builds',
        'auto_cancel_branch_builds',
        'auto_cancel_pull_request_builds',
    ];

    public const CI_PULL_REQUEST_MERGE_METHOD_MERGE = 1;

    public const CI_PULL_REQUEST_MERGE_METHOD_SQUASH = 2;

    public const CI_PULL_REQUEST_MERGE_METHOD_REBASE = 3;

    public static function enableDebug(): void
    {
        ini_set('display_errors', 'on');
        ini_set('error_reporting', (string) \constant('E_ALL'));
        ini_set('log_errors', 'on');
        ini_set('error_log', sys_get_temp_dir().'/pcit.log');
    }
}

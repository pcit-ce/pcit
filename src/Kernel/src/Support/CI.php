<?php

declare(strict_types=1);

namespace PCIT\Support;

class CI
{
    const BUILD_ACTIVATE = 1;

    const BUILD_DEACTIVATE = 0;

    const BUILD_EVENT_PUSH = 'push';

    const BUILD_EVENT_TAG = 'tag';

    const BUILD_EVENT_PR = 'pull_request';

    const BUILD_EVENT_ISSUE = 'issue';

    const GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS = 'success'; // web-passed ico-passing

    const GITHUB_CHECK_SUITE_CONCLUSION_FAILURE = 'failure'; // web-failed ico-failing

    // 中性的.
    const GITHUB_CHECK_SUITE_CONCLUSION_NEUTRAL = 'neutral';

    const GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED = 'cancelled'; // web-canceled web-errored ico-error

    const GITHUB_CHECK_SUITE_CONCLUSION_SKIPPED = 'skipped';

    const GITHUB_CHECK_SUITE_CONCLUSION_TIMED_OUT = 'timed_out';

    // 需要注意，有意外情况.
    const GITHUB_CHECK_SUITE_CONCLUSION_ACTION_REQUIRED = 'action_required';

    // status
    // 1. 将 webhooks 转化为 build，build 状态变为 pending
    // 2. 轮询 pending build，生成 job，
    // build 状态变为 queued，异常则变为 cancelled
    // job 状态变为 queued

    // 1. agent 轮询 queued job
    // 2. job 状态变为 in_progress
    // 3. 根据运行结果，变更 job 状态
    // 4. 根据 job 状态，同步 build 状态
    const GITHUB_CHECK_SUITE_STATUS_QUEUED = 'queued'; // pending web-created web-started

    const GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS = 'in_progress';

    const GITHUB_CHECK_SUITE_STATUS_COMPLETED = 'completed';

    const MEDIA_TYPE_COMMENT_BODY_RAW = 'application/vnd.github.v3.raw+json';

    const MEDIA_TYPE_COMMENT_BODY_TEXT = 'application/vnd.github.v3.text+json';

    const MEDIA_TYPE_COMMENT_BODY_HTML = 'application/vnd.github.v3.html+json';

    const MEDIA_TYPE_COMMENT_BODY_FULL = 'application/vnd.github.v3.full+json';

    const MEDIA_TYPE_GIT_BLOB_JSON = 'application/vnd.github.v3+json';

    const MEDIA_TYPE_GIT_BLOB_RAW = 'application/vnd.github.v3.raw';

    const MEDIA_TYPE_COMMITS_DIFF = 'application/vnd.github.v3.diff';

    const MEDIA_TYPE_COMMITS_PATCH = 'application/vnd.github.v3.patch';

    const MEDIA_TYPE_COMMITS_SHA = 'application/vnd.github.v3.sha';

    const MEDIA_TYPE_REPOSITORY_CONTENTS_RAW = 'application/vnd.github.v3.raw';

    const MEDIA_TYPE_REPOSITORY_CONTENTS_HTML = 'application/vnd.github.v3.html';

    const MEDIA_TYPE_GIST_RAW = 'application/vnd.github.v3.raw';

    const MEDIA_TYPE_GIST_BASE64 = 'application/vnd.github.v3.base64';

    const CI_SETTING_ARRAY = [
        'build_pushes',
        'build_pull_requests',
        'maximum_number_of_builds',
        'auto_cancel_branch_builds',
        'auto_cancel_pull_request_builds',
    ];

    const CI_PULL_REQUEST_MERGE_METHOD_MERGE = 1;

    const CI_PULL_REQUEST_MERGE_METHOD_SQUASH = 2;

    const CI_PULL_REQUEST_MERGE_METHOD_REBASE = 3;

    public static function enableDebug(): void
    {
        ini_set('display_errors', 'on');
        ini_set('error_reporting', (string) \constant('E_ALL'));
        ini_set('log_errors', 'on');
        ini_set('error_log', sys_get_temp_dir().'/pcit.log');
    }
}

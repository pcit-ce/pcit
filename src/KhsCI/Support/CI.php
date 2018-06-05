<?php

declare(strict_types=1);

namespace KhsCI\Support;

class CI
{
    const BUILD_STATUS_CANCELED = 'canceled';

    const BUILD_STATUS_PENDING = 'pending';

    const BUILD_STATUS_PASSED = 'passed';

    const BUILD_STATUS_ERRORED = 'errored';

    const BUILD_STATUS_FAILED = 'failed';

    const BUILD_STATUS_SKIP = 'skip';

    const BUILD_STATUS_IN_PROGRESS = 'in_progress';

    const BUILD_STATUS_INACTIVE = 'inactive';

    const BUILD_ACTIVATE = 1;

    const BUILD_DEACTIVATE = 0;

    const BUILD_EVENT_PUSH = 'push';

    const BUILD_EVENT_TAG = 'tag';

    const BUILD_EVENT_PR = 'pull_request';

    const BUILD_EVENT_ISSUE = 'issue';

    const GITHUB_STATUS_ERROR = 'error';

    const GITHUB_STATUS_FAILURE = 'failure';

    const GITHUB_STATUS_PENDING = 'pending';

    const GITHUB_STATUS_SUCCESS = 'success';

    const GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS = 'success';

    const GITHUB_CHECK_SUITE_CONCLUSION_FAILURE = 'failure';

    // 中性的.
    const GITHUB_CHECK_SUITE_CONCLUSION_NEUTRAL = 'neutral';

    const GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED = 'cancelled';

    const GITHUB_CHECK_SUITE_CONCLUSION_TIMED_OUT = 'timed_out';

    // 需要注意，有意外情况.
    const GITHUB_CHECK_SUITE_CONCLUSION_ACTION_REQUIRED = 'action_required';

    const GITHUB_CHECK_SUITE_STATUS_QUEUED = 'queued';

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
        'builds_only_with_khsci_yml',
        'build_pushes',
        'build_pull_requests',
        'maximum_number_of_builds',
        'auto_cancel_branch_builds',
        'auto_cancel_pull_request_builds',
    ];

    const CI_PULL_REQUEST_MERGE_METHOD_MERGE = 'merge';

    const CI_PULL_REQUEST_MERGE_METHOD_SQUASH = 'squash';

    const CI_PULL_REQUEST_MERGE_METHOD_REBASE = 'rebase';

    /**
     * 返回当前 ENV.
     *
     * 传入 env, 判断是否与当前环境匹配
     *
     * @param string|null|array $env
     *
     * @return array|false|string
     */
    public static function environment($env = null)
    {
        $current_env = getenv('APP_ENV');

        if (null === $env) {
            return $current_env;
        } elseif (is_array($env)) {
            return in_array($current_env, $env);
        }

        return $env === $current_env;
    }
}

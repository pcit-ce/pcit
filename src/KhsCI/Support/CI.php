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

    /**
     * 返回当前 ENV
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

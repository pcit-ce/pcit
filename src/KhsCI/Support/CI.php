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

    const GITHUB_CHECK_SUITE_CONCLUSION_NEUTRAL = 'neutral';

    const GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED = 'cancelled';

    const GITHUB_CHECK_SUITE_CONCLUSION_TIMED_OUT = 'timed_out';

    const GITHUB_CHECK_SUITE_CONCLUSION_ACTION_REQUIRED = 'action_required';

    const GITHUB_CHECK_SUITE_STATUS_QUEUED = 'queued';

    const GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS = 'in_progress';

    const GITHUB_CHECK_SUITE_STATUS_COMPLETED = 'completed';

    public static function environment()
    {
        return getenv('APP_ENV');
    }
}

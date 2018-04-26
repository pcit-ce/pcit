<?php

namespace KhsCI\Support;

class CIConst
{
    const BUILD_STATUS_CANCELED = 'canceled';

    const BUILD_STATUS_PASSED = 'passed';

    const BUILD_STATUS_ERRORED = 'errored';

    const BUILD_STATUS_FAILED = 'failed';

    const BUILD_ACTIVATE = 1;

    const BUILD_DEACTIVATE = 0;

    const BUILD_EVENT_PUSH = 'push';

    const BUILD_EVENT_TAG = 'tag';

    const BUILD_EVENT_PR = 'pr';

    const BUILD_EVENT_ISSUE = 'issue';
}

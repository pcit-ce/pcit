<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\CheckRun;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property "created"|"completed"|"rerequested"|"requested_action" $action
 * @property int                                                    $rid
 * @property string                                                 $repo_full_name
 * @property string                                                 $branch
 * @property string                                                 $commit_id
 * @property int                                                    $check_suite_id
 * @property int                                                    $check_run_id
 * @property string                                                 $external_id
 */
class CheckRunContext extends Context
{
    public CheckRun $check_run;

    use ContextTrait;
}

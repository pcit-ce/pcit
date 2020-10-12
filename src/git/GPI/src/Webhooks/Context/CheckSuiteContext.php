<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\CheckSuite;
use PCIT\GPI\Webhooks\Context\Components\HeadCommit;
use PCIT\GPI\Webhooks\Context\Components\User\Author;
use PCIT\GPI\Webhooks\Context\Components\User\Committer;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property "completed"|"requested"|"rerequested" $action
 * @property int                                   $check_suite_id
 * @property string                                $ref
 * @property string                                $before
 * @property string                                $after
 * @property bool                                  $created
 * @property bool                                  $forced
 * @property string                                $base_ref
 * @property string                                $compare
 * @property HeadCommit                            $head_commit
 * @property string                                $branch
 * @property string                                $commit_id
 * @property string                                $commit_message
 * @property int                                   $event_time
 * @property Author                                $author
 * @property Committer                             $committer
 */
class CheckSuiteContext extends Context
{
    public CheckSuite $check_suite;

    use ContextTrait;
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Author;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Committer;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

/**
 * @property "completed"|"requested"|"rerequested" $action
 * @property int                                   $check_suite_id
 * @property string                                $ref
 * @property string                                $before
 * @property string                                $after
 * @property Repository                            $repository
 * @property Pusher                                $pusher
 * @property Organization                          $organization
 * @property Sender                                $sender
 * @property bool                                  $created
 * @property bool                                  $forced
 * @property string                                $base_ref
 * @property string                                $compare
 * @property Commits                               $commits
 * @property HeadCommit                            $head_commit
 * @property int                                   $rid
 * @property string                                $repo_full_name
 * @property string                                $branch
 * @property string                                $commit_id
 * @property string                                $commit_message
 * @property int                                   $event_time
 * @property int                                   $installation_id
 * @property Author                                $author
 * @property Committer                             $committer
 * @property Account                               $account
 */
class CheckSuiteContext extends Context
{
}

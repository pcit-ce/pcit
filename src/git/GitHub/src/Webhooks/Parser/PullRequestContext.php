<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GitHub\Webhooks\Context;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property string  $installation_id
 * @property string  $rid
 * @property string  $repo_full_name
 * @property string  $event_time
 * @property string  $commit_message
 * @property string  $commit_id
 * @property string  $committer_username
 * @property string  $committer_uid
 * @property string  $pull_request_number
 * @property string  $branch
 * @property string  $internal
 * @property string  $pull_request_source
 * @property Account $account
 * @property string  $action
 */
class PullRequestContext extends Context
{
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Account;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Author;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Committer;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Sender;

/**
 * @property int       $rid
 * @property string    $repo_full_name
 * @property string    $branch
 * @property string    $commit_id
 * @property string    $commit_message
 * @property string    $compare
 * @property int       $event_time
 * @property int       $installation_id
 * @property Author    $author
 * @property Committer $committer
 * @property Account   $account
 * @property Sender    $sender
 * @property bool      $private
 */
class PushContext extends Context
{
}

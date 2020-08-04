<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\Author;
use PCIT\GPI\Webhooks\Context\Components\User\Committer;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;

/**
 * @property int       $rid
 * @property string    $repo_full_name
 * @property string    $branch
 * @property string    $tag
 * @property string    $commit_id
 * @property string    $commit_message
 * @property int       $installation_id
 * @property Author    $author
 * @property Committer $committer
 * @property Owner     $owner
 * @property Sender    $sender
 */
class TagContext extends Context
{
}

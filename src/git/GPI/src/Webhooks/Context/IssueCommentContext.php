<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;

/**
 * @property int                          $installation_id
 * @property int                          $rid
 * @property string                       $repo_full_name
 * @property string                       $sender_username
 * @property int                          $sender_uid
 * @property string                       $sender_pic
 * @property int                          $issue_id
 * @property int|string                   $issue_number
 * @property string                       $comment_id
 * @property string                       $body
 * @property int                          $created_at
 * @property int                          $updated_at
 * @property Owner                        $owner
 * @property "created"|"edited"|"deleted" $action
 * @property bool                         $is_pull_request
 */
class IssueCommentContext extends Context
{
}

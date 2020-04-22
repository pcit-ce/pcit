<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parser;

use PCIT\GitHub\Webhooks\Context;
use PCIT\GitHub\Webhooks\Parser\UserBasicInfo\Account;

/**
 * @property string                           $installation_id
 * @property string                           $rid
 * @property string                           $repo_full_name
 * @property string                           $sender_username
 * @property string                           $sender_uid
 * @property string                           $sender_pic
 * @property string                           $issue_id
 * @property string                           $issue_number
 * @property string                           $comment_id
 * @property string                           $body
 * @property string                           $created_at
 * @property string                           $updated_at
 * @property Account                          $account
 * @property "created" | "edited" | "deleted" $action
 * @property bool                             $is_pull_request
 */
class IssueCommentContext extends Context
{
}

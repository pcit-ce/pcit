<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Components\HeadCommit;
use PCIT\GPI\Webhooks\Context\Components\User\Author;
use PCIT\GPI\Webhooks\Context\Components\User\Committer;
use PCIT\GPI\Webhooks\Context\Components\User\Pusher;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * @property bool       $created
 * @property bool       $forced
 * @property string     $base_ref
 * @property HeadCommit $head_commit
 * @property string     $branch
 * @property string     $commit_id
 * @property string     $commit_message
 * @property int        $event_time
 * @property Author     $author
 * @property Committer  $committer
 */
class PushContext extends Context
{
    public string $ref;

    public string $before;

    public string $after;

    public string $compare;

    /** @var \PCIT\GPI\Webhooks\Context\Components\Commits[] */
    public array $commits;

    public Pusher $pusher;

    public HeadCommit $head_commit;

    use ContextTrait;
}

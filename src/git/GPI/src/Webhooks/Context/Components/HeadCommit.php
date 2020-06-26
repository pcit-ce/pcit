<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Author;
use PCIT\GPI\Webhooks\Parser\UserBasicInfo\Committer;

/**
 * @property string        $id
 * @property string        $message
 * @property string        $timestamp
 * @property int           $timestamp_int
 * @property Author        $author
 * @property Committer     $committer
 * @property array<string> $added
 * @property array<string> $removed
 * @property array<string> $modified
 */
class HeadCommit extends Kernel
{
}

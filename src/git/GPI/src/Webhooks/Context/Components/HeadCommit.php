<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\Framework\Support\Date;
use PCIT\GPI\Webhooks\Context\Components\User\Author;
use PCIT\GPI\Webhooks\Context\Components\User\Committer;

/**
 * @property string        $id
 * @property string        $tree_id
 * @property string        $message
 * @property string        $timestamp
 * @property int           $timestamp_int
 * @property array<string> $added
 * @property array<string> $removed
 * @property array<string> $modified
 */
class HeadCommit
{
    public Author $author;

    public Committer $committer;

    public function __get(string $name)
    {
        if ('timestamp_int' === $name) {
            return $this->timestamp_int = $this->timestamp_int ?? Date::parse($this->timestamp);
        }
    }
}

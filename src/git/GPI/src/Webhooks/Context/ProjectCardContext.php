<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class ProjectCardContext
{
    /**
     * The changes to the project card if the action was `edited` or `converted`.
     */
    public $changes;

    /**
     * The id of the card that this card now follows if the action was "moved". Will be null if it is the first card in a column.
     */
    public ?int $after_id;

    public $project_card;

    use ContextTrait;
}

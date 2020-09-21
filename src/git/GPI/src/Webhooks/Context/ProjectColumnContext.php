<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

class ProjectColumnContext extends Context
{
    /**
     * The changes to the project column if the action was `edited`.
     */
    public $changes;

    /**
     * The id of the column that this column now follows if the action was "moved". Will be null if it is the first column in a project.
     */
    public ?int $after_id;

    public $project_column;

    use ContextTrait;
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context;

use PCIT\GPI\Webhooks\Context;
use PCIT\GPI\Webhooks\Context\Traits\ContextTrait;

/**
 * A Git branch or tag is deleted.
 *
 * @property int            $rid
 * @property string         $repo_full_name
 * @property "branch"|"tag" $ref_type
 */
class DeleteContext extends Context
{
    /**
     * refs/heads/<branch>
     * refs/tags/<tag>.
     */
    public string $ref;

    public string $ref_type;

    use ContextTrait;
}

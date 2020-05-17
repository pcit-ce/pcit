<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Parser\UserBasicInfo;

class Committer extends Author
{
    public function __construct($committer)
    {
        parent::__construct($committer);
    }
}

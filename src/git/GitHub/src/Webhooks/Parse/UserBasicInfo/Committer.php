<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Parse\UserBasicInfo;

class Committer extends Author
{
    public function __construct($committer)
    {
        parent::__construct($committer);
    }
}

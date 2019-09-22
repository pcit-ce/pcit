<?php

declare(strict_types=1);

namespace PCIT\GitHub\WebhooksParse\UserBasicInfo;

class Committer extends Author
{
    public function __construct($committer)
    {
        parent::__construct($committer);
    }
}

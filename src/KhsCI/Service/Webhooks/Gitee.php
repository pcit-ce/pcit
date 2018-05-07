<?php

declare(strict_types=1);

namespace KhsCI\Service\Webhooks;

class Gitee extends GitHub
{
    private static $git_type = 'gitee';

    public function __construct(string $access_token = null)
    {
        parent::__construct($access_token);
    }
}

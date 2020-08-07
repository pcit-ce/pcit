<?php

declare(strict_types=1);

namespace PCIT\Gitee\Webhooks\Handler;

use PCIT\Gitee\Webhooks\Parser\Push as PushParser;
use PCIT\GPI\Webhooks\Handler\Abstracts\PushAbstract;

class Push extends PushAbstract
{
    public $git_type = 'gitee';

    public function handle(string $webhooks_content): void
    {
        $context = PushParser::handle($webhooks_content);

        $this->pustomize($context, $this->git_type);
    }
}

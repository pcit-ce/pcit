<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Context\TagContext;
use PCIT\GPI\Webhooks\Handler\Abstracts\PushAbstract;

class Push extends PushAbstract
{
    /**
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Push::handle($webhooks_content);

        if ($context->tag ?? null) {
            $this->tag($context);

            return;
        }

        return;

        $this->handlePush($context, 'github');
    }

    /**
     * @throws \Exception
     */
    public function tag(TagContext $context): void
    {
        // tag 删除也会触发 push 事件
        if ('0000000000000000000000000000000000000000' === $context->commit_id) {
            return;
        }

        $this->handleTag($context, 'github');
    }
}

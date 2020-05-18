<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

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

        $this->handlePush($context);
    }

    /**
     * @throws \Exception
     */
    public function tag($context): void
    {
        $this->handleTag($context);
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks;

class PustomizeHandler
{
    public function handle(string $class, ContextInterface $context): void
    {
        $class = 'PCIT\\Pustomize\\'.$class.'\\Handler';

        if (!class_exists($class)) {
            \Log::info('Pustomize: '.$class.' not found');

            return;
        }

        \Log::info('Pustomize: '.$class.' trigger');

        (new $class())->handle($context);
    }
}

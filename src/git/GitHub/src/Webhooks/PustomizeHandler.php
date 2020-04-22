<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks;

class PustomizeHandler
{
    public function handle(string $class, ContextInterface $context): void
    {
        if (!class_exists($class)) {
            \Log::info('Pustomize: '.$class.' not found');

            return;
        }

        \Log::info('Pustomize: '.$class.' trigger');

        (new $class())->handle($context);
    }
}

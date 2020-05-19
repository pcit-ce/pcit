<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks;

abstract class Context implements ContextInterface
{
    public $raw;
    public $context_array;
    public $git_type;

    public function __construct(array $context_array, string $raw)
    {
        $this->raw = $raw;
        $this->context_array = $context_array;
    }

    public function __get(string $name)
    {
        return $this->context_array[$name] ?? (json_decode($this->raw)->$name) ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->context_array[$name] = $value;
    }
}

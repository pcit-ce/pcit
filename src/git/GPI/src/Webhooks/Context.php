<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks;

abstract class Context implements ContextInterface
{
    /** 原始 webhook 内容 */
    public $raw;
    public $context_array;

    /** git 提供者 */
    public $git_type;

    /** 是否为私有仓库 */
    public $private;

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

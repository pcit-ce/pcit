<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

class InstallationRepositories
{
    public int $id;
    public string $name;
    public string $full_name;
    public bool $private;
}

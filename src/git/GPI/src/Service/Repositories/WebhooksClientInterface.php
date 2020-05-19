<?php

declare(strict_types=1);

namespace PCIT\GPI\Service\Repositories;

interface WebhooksClientInterface
{
    public function getWebhooks(bool $raw, string $username, string $repo);

    public function getStatus(string $url, string $username, string $repo);

    public function setWebhooks($data, string $username, string $repo, ?string $id = null);

    public function unsetWebhooks(string $username, string $repo, string $id);
}

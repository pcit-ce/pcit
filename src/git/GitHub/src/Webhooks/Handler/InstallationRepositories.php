<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\InstallationRepositoriesAbstract;

class InstallationRepositories extends InstallationRepositoriesAbstract
{
    public $git_type = 'github';

    /**
     * 用户对仓库的操作.
     *
     * added 用户增加仓库
     *
     * removed 移除仓库
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\InstallationRepositories::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}

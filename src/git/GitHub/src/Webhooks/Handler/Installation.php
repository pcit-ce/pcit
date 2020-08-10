<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks\Handler;

use PCIT\GPI\Webhooks\Handler\Abstracts\InstallationAbstract;

class Installation extends InstallationAbstract
{
    public $git_type = 'github';

    /**
     * created 用户点击安装按钮(首次).
     *
     * deleted 用户卸载了 GitHub Apps
     *
     * @throws \Exception
     */
    public function handle(string $webhooks_content): void
    {
        $context = \PCIT\GitHub\Webhooks\Parser\Installation::handle($webhooks_content);

        $this->pustomize($context, 'github');
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\Tests\Webhooks\Parser;

// use Tests\TestCase;
use PCIT\GitHub\Webhooks\Parser\Installation;
use PCIT\GPI\Webhooks\Context\Components\InstallationRepositories;
use PCIT\GPI\Webhooks\Context\Components\User\Account;
use PHPUnit\Framework\TestCase;

class InstallationTest extends TestCase
{
    public function test_handle(): void
    {
        $context = Installation::handle(file_get_contents(__DIR__.'/../github_app/installation_created.json'));

        $this->assertTrue($context->installation->account instanceof Account);
        $this->assertTrue($context->repositories[0] instanceof InstallationRepositories);
    }

    public function test_handle_deleted(): void
    {
        $context = Installation::handle(file_get_contents(__DIR__.'/../github_app/installation_deleted.json'));

        $this->assertTrue($context->installation->account instanceof Account);
        //$this->assertTrue($context->repositories[0] instanceof InstallationRepositories);

        // var_dump($context);
    }
}

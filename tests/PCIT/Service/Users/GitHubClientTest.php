<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Users;

use KhsCI\Support\Env;
use PCIT\Tests\PCITTestCase;

class GitHubClientTest extends PCITTestCase
{
    /**
     * @group dont-test
     *
     * @throws \Exception
     */
    public function testAuthorizations(): void
    {
        $pcit = self::getTest();

        $username = Env::get('CI_GITHUB_TEST_USERNAME');

        $password = Env::get('CI_GITHUB_TEST_PASSWORD');

        $result = $pcit->user_basic_info->getUserInfo();
    }
}

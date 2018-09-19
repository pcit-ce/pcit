<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Users;

use KhsCI\Support\Env;
use KhsCI\Tests\KhsCITestCase;

class TestGitHubClient extends KhsCITestCase
{
    /**
     * @group DON'TTEST
     *
     * @throws \Exception
     */
    public function testAuthorizations(): void
    {
        $khsci = self::getTest();

        $username = Env::get('CI_GITHUB_TEST_USERNAME');

        $password = Env::get('CI_GITHUB_TEST_PASSWORD');

        $khsci->user_basic_info->authorizations($username, $password);
    }
}

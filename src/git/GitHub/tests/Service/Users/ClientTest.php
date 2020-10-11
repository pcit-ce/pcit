<?php

declare(strict_types=1);

namespace PCIT\GitHub\Tests\Service\Users;

use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * @group dont-test
     */
    public function testAuthorizations(): void
    {
        $username = env('CI_GITHUB_TEST_USERNAME');

        $password = env('CI_GITHUB_TEST_PASSWORD');

        $result = app('pcit')->user_basic_info->getUserInfo();
    }
}

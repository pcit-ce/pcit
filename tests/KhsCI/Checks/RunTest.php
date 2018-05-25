<?php

namespace Khsci\Tests\Checks;

use App\Console\Migrate;
use App\Console\Up;
use Exception;
use KhsCI\Support\CI;
use KhsCI\Tests\KhsCITestCase;

class RunTest extends KhsCITestCase
{
    /**
     * @throws Exception
     */
    public function setUp()
    {
        Migrate::all();

        Up::setGitType('github_app');

        Up::installation_repositories(file_get_contents(
            __DIR__.'/../Webhooks/github_app/installation_repositories_add.json'));

        Up::push(file_get_contents(__DIR__.'/../Webhooks/github/push.json'));
    }

    /**
     * @group DON'TTEST
     * @throws Exception
     */
    public function testCancelled()
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
    }

    /**
     * @group DON'TTEST
     * @throws Exception
     */
    public function testFailure()
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
    }

    /**
     * @throws Exception
     */
    public function tearDown()
    {
        Migrate::cleanup();
    }
}

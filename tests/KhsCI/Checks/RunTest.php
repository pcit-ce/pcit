<?php

declare(strict_types=1);

namespace Khsci\Tests\Checks;

use App\Console\Migrate;
use App\Console\Up;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\CI;
use KhsCI\Tests\KhsCITestCase;

class RunTest extends KhsCITestCase
{
    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::__construct();

        Migrate::all();

        Up::setGitType('github_app');

        Up::installation_repositories(file_get_contents(
            __DIR__.'/../Webhooks/github_app/installation_repositories_add.json'));

        Up::push(file_get_contents(__DIR__.'/../Webhooks/github/push.json'));
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testSuccess(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testFailure(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testNeutral(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_NEUTRAL);
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testCancelled(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testTimedOut(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_TIMED_OUT);
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testActionRequired(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_ACTION_REQUIRED);
    }

    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testAction(): void
    {
        Up::setGitType('github_app');

        Up::updateGitHubAppChecks('1', null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(), CI::GITHUB_CHECK_SUITE_CONCLUSION_NEUTRAL,
            null, null, null, null, null,
            [(new KhsCI())->check_run::createAction()]);
    }

    /**
     * @throws Exception
     */
    public function tearDown(): void
    {
        Migrate::cleanup();

        $this->assertEquals(0, 0);
    }
}

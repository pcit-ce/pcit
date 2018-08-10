<?php

declare(strict_types=1);

namespace App\Console\Events;

class UpdateBuildStatus
{
    public $build_status;

    public $build;

    public function __construct(Build $build, string $build_status)
    {
        $this->build = $build;

        $this->build_status = $build_status;
    }

    public function handle(): void
    {
        switch ($this->build_status) {
            case 'inactive':
                $this->build_status = 'inactive';
                $this->setBuildStatusInactive();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE;
                (new Failed())->handle();
                $this->setBuildStatusFailed();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS;
                (new Passed())->handle();

                break;
            default:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED;
                (new Cancelled())->handle();
        }
    }
}

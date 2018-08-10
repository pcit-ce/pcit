<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Events\Build;
use App\Console\Events\CheckAdmin;
use App\Console\Events\Subject;
use App\Console\Events\UpdateBuildStatus;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\CI;

class BuildCommand
{
    /**
     * @throws Exception
     */
    public function build(): void
    {
        $khsci = new KhsCI();

        // get build info
        $buildData = (new Build())->handle();

        $build = $khsci->build;

        $subject = new Subject();

        $subject
            // check ci root
            ->register(new CheckAdmin($buildData))
            // update build status in progress
            ->register(new UpdateBuildStatus(
                $buildData->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS))
            ->handle();

        // exec build
        $build->handle($buildData);
    }
}

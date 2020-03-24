<?php

declare(strict_types=1);

namespace App\Events;

use App\Repo;
use App\User;
use PCIT\Exception\PCITException;
use PCIT\Runner\BuildData;
use PCIT\Support\CI;

/**
 * 检查仓库是否位于管理员名下.
 */
class CheckAdmin
{
    public $build;

    public function __construct(BuildData $build)
    {
        $this->build = $build;
    }

    /**
     * @throws PCITException
     * @throws \Exception
     */
    public function handle(): void
    {
        $build = $this->build;

        $ci_root = env('CI_ROOT');

        while ($ci_root) {
            \Log::info('ci root(admin) already set, only ci root\'s repo can run!', []);

            $admin = Repo::getAdmin((int) $build->rid, false, $build->git_type);

            if (!$admin) {
                \Log::warning('repo admin not found', []);

                goto a;
            }

            $admin_array = json_decode($admin, true);

            $ci_root_array = json_decode($ci_root, true);
            $root = $ci_root_array[$build->git_type];

            foreach ($root as $k) {
                $uid = User::getUid($k, $build->git_type);

                if (\in_array($uid, $admin_array, true)) {
                    \Log::info('This repo is ci root\'s repo, continue...', []);

                    return;
                }
            }

            \Log::warning('This repo is not ci root\'s repo, skip', []);

            a:

            throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS, $build->build_key_id);
        }
    }
}

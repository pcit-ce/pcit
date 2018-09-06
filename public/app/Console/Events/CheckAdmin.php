<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Repo;
use App\User;
use KhsCI\CIException;
use KhsCI\Service\Build\BuildData;
use KhsCI\Support\CI;
use KhsCI\Support\Env;
use KhsCI\Support\Log;

class CheckAdmin
{
    /**
     * @var Build
     */
    public $build;

    public function __construct(BuildData $build)
    {
        $this->build = $build;
    }

    /**
     * @throws CIException
     * @throws \Exception
     */
    public function handle(): void
    {
        $build = $this->build;

        $ci_root = Env::get('CI_ROOT');

        while ($ci_root) {
            Log::debug(__FILE__, __LINE__, 'KhsCI already set ci root', [], Log::INFO);

            $admin = Repo::getAdmin((int) $build->rid, false, $build->git_type);

            if (!$admin) {
                Log::debug(__FILE__, __LINE__, 'repo admin not found', [], LOG::WARNING);

                goto a;
            }

            $admin_array = json_decode($admin, true);

            $ci_root_array = json_decode($ci_root, true);
            $root = $ci_root_array[$build->git_type];

            foreach ($root as $k) {
                $uid = User::getUid($k, $build->git_type);

                if (in_array($uid, $admin_array)) {
                    Log::debug(__FILE__, __LINE__, 'This repo is ci root\'s repo, building...', [], Log::INFO);

                    return;
                }
            }

            Log::debug(__FILE__, __LINE__, 'This repo is not ci root\'s repo, skip', [], Log::WARNING);

            a:

            throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS, $build->build_key_id);
        }
    }
}

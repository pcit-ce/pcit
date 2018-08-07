<?php

declare(strict_types=1);

namespace App\Console\Webhooks;

use App\Build;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Log;

class Skip
{
    /**
     * 检查 commit 信息跳过构建. branch 匹配构建.
     *
     * @param null|string $commit_message
     * @param int         $build_key_id
     * @param string      $branch
     * @param string      $config
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function handle(?string $commit_message, int $build_key_id, string $branch = null, string $config = null)
    {
        // check commit message
        if (preg_match('#(\[skip ci\])|(\[ci skip\])#i', $commit_message)) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' is skip by commit message', [], Log::INFO);

            return true;
        }

        if (null === $config) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because config is empty', [], Log::INFO);

            return false;
        }

        $yaml_obj = (object) json_decode($config, true);

        $branches = $yaml_obj->branches ?? null;

        if (null === $branches) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because branches is empty', [], Log::INFO);

            return false;
        }

        $branches_exclude = $branches['exclude'] ?? [];

        $branches_include = $branches['include'] ?? [];

        if ([] === $branches_exclude and [] === $branches_include) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because branches is empty', [], Log::INFO);

            return false;
        }

        // 匹配排除分支
        if ($branches_exclude) {
            if ((new KhsCI())->build::check($branches_exclude, $branch)) {
                $message = "config exclude branch $branch, build skip  ";

                Log::debug(__FILE__, __LINE__, $message, [], Log::INFO);

                return true;
            }
        }

        // 匹配包含分支
        if ($branches_include) {
            if ((new KhsCI())->build::check($branches_include, $branch)) {
                $message = "config include branch $branch, building  ";

                Log::debug(__FILE__, __LINE__, $message, [], Log::INFO);

                return false;
            }
        }

        return false;
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    public static function writeSkipToDB(int $build_key_id): void
    {
        Build::updateBuildStatus($build_key_id, 'skip');
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Webhooks;

use App\Build;
use Exception;
use KhsCI\Support\Log;

class Skip
{
    private $commit_message;
    private $build_key_id;
    private $branch;
    private $config;

    /**
     * Skip constructor.
     *
     * @param null|string $commit_message
     * @param int         $build_key_id
     * @param string|null $branch
     * @param string|null $config
     */
    public function __construct(?string $commit_message,
                                int $build_key_id,
                                string $branch = null,
                                string $config = null)
    {
        $this->commit_message = $commit_message;
        $this->build_key_id = $build_key_id;
        $this->branch = $branch;
        $this->config = $config;
    }

    /**
     * 检查 commit 信息跳过构建. branch 匹配构建.
     *
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $build_key_id = $this->build_key_id;
        $config = $this->config;
        $branch = $this->branch;

        // check commit message
        if (preg_match('#(\[skip ci\])|(\[ci skip\])#i', $this->commit_message)) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' is skip by commit message', [], Log::INFO);

            self::writeSkipToDB($build_key_id);

            return;
        }

        if (null === $config) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because config is empty', [], Log::INFO);

            return;
        }

        $yaml_obj = (object) json_decode($config, true);

        $branches = $yaml_obj->branches ?? null;

        if (null === $branches) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because branches is empty', [], Log::INFO);

            return;
        }

        $branches_exclude = $branches['exclude'] ?? [];

        $branches_include = $branches['include'] ?? [];

        if ([] === $branches_exclude and [] === $branches_include) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because branches is empty', [], Log::INFO);

            return;
        }

        // 匹配排除分支
        if ($branches_exclude) {
            if (self::check($branches_exclude, $branch)) {
                $message = ".khsci.yml exclude branch $branch, build skip";

                Log::debug(__FILE__, __LINE__, $message, [], Log::INFO);

                self::writeSkipToDB($build_key_id);

                return;
            }
        }

        // 匹配包含分支
        if ($branches_include) {
            if (self::check($branches_include, $branch)) {
                $message = ".khsci.yml include branch $branch, building";

                Log::debug(__FILE__, __LINE__, $message, [], Log::INFO);

                return;
            }
        }
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    public static function writeSkipToDB(int $build_key_id): void
    {
        Build::updateBuildStatus($build_key_id, 'skip');

        throw new Exception('skip');
    }

    /**
     * @param string|array $pattern
     * @param string       $subject
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function check($pattern, string $subject)
    {
        if (is_string($pattern)) {
            return self::checkString($pattern, $subject);
        }

        if (is_array($pattern)) {
            foreach ($pattern as $k) {
                if (self::checkString($k, $subject)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return bool
     */
    private static function checkString(string $pattern, string $subject)
    {
        if (preg_match('#'.$pattern.'#', $subject)) {
            return true;
        }

        return false;
    }
}

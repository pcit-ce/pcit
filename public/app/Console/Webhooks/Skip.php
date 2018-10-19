<?php

declare(strict_types=1);

namespace App\Console\Webhooks;

use App\Build;
use Exception;
use PCIT\Service\Build\Conditional\Branch;
use PCIT\Support\Log;

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

        // check commit message
        if (preg_match('#(\[skip ci\])|(\[ci skip\])#i', $this->commit_message)) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' is skip by commit message', [], Log::INFO);

            self::writeSkipToDB($build_key_id);

            return;
        }

        if (null === $this->config) {
            Log::debug(__FILE__, __LINE__, $build_key_id.' not skip, because config is empty', [], Log::INFO);

            return;
        }

        $yaml_obj = json_decode($this->config);

        $branches = $yaml_obj->branches ?? null;

        $result = (new Branch($branches, $this->branch))->regHandle();

        if ($result) {
            return;
        }

        self::writeSkipToDB($build_key_id);
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    public static function writeSkipToDB(int $build_key_id): void
    {
        Build::updateBuildStatus($build_key_id, 'skip');

        throw new Exception('build skip by commit message or branch ruler');
    }
}

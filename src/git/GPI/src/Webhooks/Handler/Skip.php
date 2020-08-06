<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Handler;

use App\Build;
use PCIT\Runner\Conditional\Branch;

/**
 * 是否跳过构建.
 */
class Skip
{
    private $commit_message;

    private $build_key_id;

    private $branch;

    private $config;

    /**
     * Skip constructor.
     */
    public function __construct(
        ?string $commit_message,
        int $build_key_id,
        string $branch = null,
        string $config = null
    )
    {
        $this->commit_message = $commit_message;
        $this->build_key_id = $build_key_id;
        $this->branch = $branch;
        $this->config = $config;
    }

    /**
     * 检查 commit 信息跳过构建. branch 匹配构建.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $build_key_id = $this->build_key_id;

        // check config
        if (null === $this->config || '[]' === $this->config) {
            \Log::info($build_key_id.' skip, because config is empty', []);

            $this->writeSkipToDB(true);

            return;
        }

        // check commit message
        if (preg_match(
            '#(\[skip ci\])|(\[ci skip\])|(\[pcit skip\])|(\[skip pcit\])#i',
            $this->commit_message
        )) {
            \Log::info($build_key_id.' is skip by commit message', []);

            $this->writeSkipToDB();

            return;
        }

        // check branch
        $yaml_obj = json_decode($this->config);
        $branches = $yaml_obj->branches ?? null;
        $result = (new Branch($branches, $this->branch))->handle(true);

        if (!$result) {
            $this->writeSkipToDB();

            return;
        }

        Build::updateBuildStatus($this->build_key_id, 'pending');
    }

    /**
     * @param bool $noConfig
     *
     * @throws \Exception
     */
    private function writeSkipToDB($noConfig = false): void
    {
        if ($noConfig) {
            Build::updateBuildStatus($this->build_key_id, 'misconfigured');

            return;
        }

        Build::updateBuildStatus($this->build_key_id, 'skip');
    }
}

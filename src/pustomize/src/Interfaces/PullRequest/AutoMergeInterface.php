<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Interfaces\PullRequest;

interface AutoMergeInterface
{
    /**
     * 根据接收到 pr 评论（$body）,决定如何合并 PR
     * 合并: [body,merge_method]
     * 不合并: [].
     */
    public function handle(string $body): array;
}

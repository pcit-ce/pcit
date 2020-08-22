<?php

declare(strict_types=1);

namespace PCIT\Runner\RPC;

/**
 * @method static getEnv(int $job_id)
 * @method static create(int $build_id)
 * @method static updateEnv(int $job_id, string $env)
 * @method static updateBuildStatus(int $job_key_id, ?string $status)
 * @method static getRid(int $job_id)
 * @method static getGitType(int $job_key_id)
 * @method static isPrivate(int $job_id)
 * @method static updateStartAt(int $job_id, ?int $time)
 * @method static updateFinishedAt(int $job_id, ?int $time)
 * @method static getRepoFullName(int $job_key_id)
 * @method static getQueuedJob()
 */
class Job extends Kernel
{
    const NAMESPACE = 'App';
}

<?php

declare(strict_types=1);

namespace PCIT\Pustomize\RepositoryDispatch;

use App\Build;
use App\GetAccessToken;
use App\Repo;
use JsonSchema\Constraints\BaseConstraint;
use PCIT\Config\Validator;
use PCIT\GetConfig;
use PCIT\GPI\Webhooks\Context\RepositoryDispatchContext;
use PCIT\PCIT;
use PCIT\Subject;
use PCIT\Support\CI;
use Symfony\Component\Yaml\Yaml;

class Handler
{
    public function handle(RepositoryDispatchContext $context)
    {
        $client_payload = $context->client_payload;

        $branch = $client_payload->branch ?? null;

        $config = $client_payload->config ?? null;

        $repository = $context->repository;

        $rid = $repository->id;
        $git_type = $context->git_type;

        $token = GetAccessToken::byRid($rid, $git_type);

        /** @var \PCIT\GPI\GPI */
        $pcit = app(PCIT::class)->git($git_type, $token);

        $branchDetail = $pcit->repo_branches->get(
            $repository->owner->login,
            $repository->name,
            $branch
        );

        $branchDetail = json_decode($branchDetail);

        $compare = null;
        $commit = $branchDetail->commit ?? null;

        if (!$commit) {
            throw new \Exception('git repo empty, please commit to git repo', 404);
        }

        $commit_id = $commit->sha;
        $commit_message = $commit->commit->message;
        $committer = $commit->commit->committer;
        $author = $commit->commit->author;
        $event_time = time();

        if ($config) {
            $config_array = Yaml::parse($config);

            $config = json_encode($config_array);

            $validator = new Validator();

            $result = $validator->validate(
                BaseConstraint::arrayToObjectRecursive($config_array)
            );

            if ([] !== $result) {
                $config = '[]';
            }
        } else {
            try {
                $subject = new Subject();
                $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;
                $config = json_encode($config_array);
            } catch (\Throwable $e) {
                // throw new \Exception($e->getMessage());
                $config = '[]';
            }
        }
        // TODO: 判断是否为私有仓库
        $last_insert_id = Build::insert(
            CI::BUILD_EVENT_REPOSITORY_DISPATCH,
            $branch,
            $compare,
            $commit_id,
            $commit_message,
            $committer->name,
            $committer->email,
            $committer->name,
            $author->name,
            $author->email,
            $author->name,
            $rid,
            $event_time,
            $config,
            $repository->private,
            $git_type,
            true
        );

        // trigger build 不检测是否跳过
        // 检查是否有配置文件 .pcit.yml

        $status = '[]' === $config ? 'misconfigured' : 'pending';
        Repo::updateRepoInfo(
            $rid,
            $repository->full_name,
            null,
            null,
            $repository->default_branch,
            $repository->private,
            $git_type
        );

        Build::updateBuildStatus((int) $last_insert_id, $status);

        \Log::info('build '.$last_insert_id.' is '.$status);

        return ['build_id' => $last_insert_id];
    }
}

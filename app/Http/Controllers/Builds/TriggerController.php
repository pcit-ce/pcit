<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Console\Webhooks\GetConfig;
use App\Console\Webhooks\GitHub\Subject;
use App\GetAccessToken;
use App\Http\Controllers\Users\JWTController;
use PCIT\PCIT;
use Symfony\Component\Yaml\Yaml;

class TriggerController
{
    /**
     * 用户指定分支构建.
     *
     * @param mixed ...$args
     *
     * @return array
     *
     * @throws \Exception
     */
    public function __invoke(...$args)
    {
        list($username, $repo_name, $branch) = $args;

        list($rid) = JWTController::checkByRepo($username, $repo_name);

        $token = GetAccessToken::getGitHubAppAccessToken(
            null, $username.'/'.$repo_name);

        $app = new PCIT(['github_access_token' => $token], 'github');

        $result = $app->repo_branches->get($username, $repo_name, $branch);

        $result = json_decode($result);

        $compare = null;
        $commit = $result->commit;
        $commit_id = $commit->sha;
        $commit_message = $commit->commit->message;
        $committer = $commit->commit->committer;
        $author = $commit->commit->author;
        $event_time = time();

        $config = file_get_contents('php://input');

        if ($config) {
            $config = json_encode(Yaml::parse($config));
        } else {
            $subject = new Subject();
            $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;
            $config = json_encode($config_array);
        }
        $last_insert_id = Build::insert('push', $branch, $compare, $commit_id,
            $commit_message, $committer->name, $committer->email, $committer->name,
            $author->name, $author->email, $author->name,
            $rid, $event_time, $config);

        // trigger build 不检测是否跳过

        Build::updateBuildStatus((int) $last_insert_id, 'pending');

        return ['build_id' => $last_insert_id];
    }
}

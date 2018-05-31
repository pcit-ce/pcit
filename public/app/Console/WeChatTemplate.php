<?php

declare(strict_types=1);

namespace App\Console;

use App\Build;
use App\Repo;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Date;
use KhsCI\Support\Git;
use KhsCI\Support\Log;

class WeChatTemplate
{
    /**
     * @param int    $build_key_id
     * @param string $info
     *
     * @throws Exception
     */
    public static function send(int $build_key_id, string $info): void
    {
        $khsci = new Khsci();

        $output = Build::find($build_key_id);

        list(
            'build_status' => $build_status,
            'finished_at' => $time,
            'event_type' => $event_type,
            'rid' => $rid,
            'branch' => $branch,
            'commit_message' => $commit_message,
            'committer_username' => $committer_username,
            'git_type' => $git_type,
            'commit_id' => $commit_id
            ) = $output;

        $repo_full_name = Repo::getRepoFullName($git_type, (int) $rid);

        $output = $khsci->wechat_template_message->sendTemplateMessage(
            $build_status,
            Date::Int2ISO((int) $time),
            $event_type,
            $repo_full_name,
            $branch,
            substr($commit_message, 0, 60),
            $committer_username,
            $info,
            Git::getCommitUrl($git_type, $repo_full_name, $commit_id)
        );

        Log::debug(__FILE__, __LINE__, $output);
    }
}

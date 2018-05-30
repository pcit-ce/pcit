<?php

namespace App\Console;

use App\Build;
use App\Repo;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Date;
use KhsCI\Support\Git;

class WeChatTemplate
{
    /**
     * @param int    $build_key_id
     *
     * @param string $info
     *
     * @throws Exception
     */
    public static function send(int $build_key_id, string $info)
    {
        $khsci = new Khsci();

        $array = Build::find($build_key_id);

        foreach ($array as $k) {

            list(
                'build_status' => $build_status,
                'finished_at' => $time,
                'event_type' => $event_type,
                'rid' => $rid,
                'branch' => $branch,
                'committer_name' => $committer,
                'git_type' => $git_type,
                ) = $k;

            $repo_full_name = Repo::getRepoFullName($git_type, (int) $rid);

            $output = $khsci->wechat_template_message->sendTemplateMessage(
                $build_status,
                Date::Int2ISO($time),
                $event_type,
                $repo_full_name,
                $branch,
                $committer,
                $info,
                Git::getUrl($git_type, $repo_full_name)
            );

            break;
        }
    }
}

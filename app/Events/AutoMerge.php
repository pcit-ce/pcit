<?php

declare(strict_types=1);

namespace App\Events;

use App\GetAccessToken;
use App\Repo;
use PCIT\PCIT;
use PCIT\Runner\BuildData;
use PCIT\Support\CI;

/**
 * CI 测试通过，自动合并.
 */
class AutoMerge
{
    public $build;

    public function __construct(BuildData $build)
    {
        $this->build = $build;
    }

    public function handle(): void
    {
        $build = $this->build;

        \Log::info('check auto merge', []);

        $build_status = $build->build_status;

        $pcit = app(PCIT::class)->setGitType($build->git_type)
        ->setAccessToken(GetAccessToken::getGitHubAppAccessToken($build->rid));

        $auto_merge_label = $pcit
            ->issue_labels
            ->listLabelsOnIssue($build->repo_full_name, $build->pull_request_number);

        $auto_merge_method = '';

        if ((CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $build_status) && $auto_merge_label) {
            \Log::info('already set auto merge', []);

            $repo_array = explode('/', Repo::getRepoFullName($build->rid, $build->git_type));

            try {
                if ($pcit->pull_request->isMerged($repo_array[0], $repo_array[1], $build->pull_request_number)) {
                    \Log::warning('already merged, skip', []);

                    return;
                }

                $commit_message = null;

                $pcit->pull_request
                    ->merge(
                        $repo_array[0],
                        $repo_array[1],
                        $build->pull_request_number,
                        $build->commit_message,
                        $commit_message,
                        $build->commit_id,
                        (int) $auto_merge_method
                    );
                \Log::info('auto merge success, method is '.$auto_merge_method, []);
            } catch (\Throwable $e) {
                \Log::debug($e->__toString());
            }
        }
    }
}

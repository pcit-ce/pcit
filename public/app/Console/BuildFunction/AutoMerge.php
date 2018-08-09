<?php

declare(strict_types=1);

namespace App\Console\BuildFunction;

use App\GetAccessToken;
use App\Repo;
use KhsCI\KhsCI;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class AutoMerge
{
    /**
     * @var Build
     */
    public $build;

    public function __construct(Build $build)
    {
        $this->build = $build;
    }

    /**
     * @param Build $build
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $build = $this->build;

        Log::debug(__FILE__, __LINE__, 'check auto merge', [], Log::INFO);

        $build_status = $build->build_status;

        $khsci = new KhsCI([
            $build->git_type.'_access_token' => GetAccessToken::getGitHubAppAccessToken($build->rid),
        ]);

        $auto_merge_label = $khsci
            ->issue_labels
            ->listLabelsOnIssue($build->repo_full_name, $build->pull_request_number);

        $auto_merge_method = '';

        if ((CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $build_status) && $auto_merge_label) {
            Log::debug(__FILE__, __LINE__, 'already set auto merge', [], Log::INFO);

            $repo_array = explode('/', Repo::getRepoFullName($build->rid, $build->git_type));

            try {
                if ($khsci->pull_request->isMerged($repo_array[0], $repo_array[1], $build->pull_request_number)) {
                    Log::debug(
                        __FILE__,
                        __LINE__,
                        'already merged, skip', [], Log::WARNING
                    );

                    return;
                }

                $commit_message = null;

                $khsci->pull_request
                    ->merge(
                        $repo_array[0],
                        $repo_array[1],
                        $build->pull_request_number,
                        $build->commit_message,
                        $commit_message,
                        $build->commit_id,
                        (int) $auto_merge_method
                    );
                Log::debug(__FILE__, __LINE__, 'auto merge success, method is '.$auto_merge_method, [], Log::INFO);
            } catch (\Throwable $e) {
                Log::debug(__FILE__, __LINE__, $e->__toString());
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace KhsCI\Service\PullRequest;

use Exception;
use KhsCI\Service\CICommon;

/**
 * Class GitHubClient.
 *
 * @see https://developer.github.com/v3/pulls/
 */
class GitHubClient
{
    use CICommon;

    private $is_update;

    private $header = [
        'Accept' => 'application/vnd.github.symmetra-preview+json',
    ];

    /**
     * @param string $username
     * @param string $repo_name
     * @param string $state     Either open, closed, or all to filter by state. Default: open
     * @param string $head
     * @param string $base
     * @param string $sort      What to sort results by. Can be either created, updated, popularity (comment count) or
     *                          long-running (age, filtering by pulls updated in the last month). Default: created
     * @param string $direction The direction of the sort. Can be either asc or desc. Default: desc when sort is
     *                          created or sort is not specified, otherwise asc.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $username,
                         string $repo_name,
                         string $state = null,
                         string $head = null,
                         string $base = null,
                         string $sort = null,
                         string $direction = null)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls']);

        $data = [
            'state' => $state,
            'head' => $head,
            'base' => $base,
            'sort' => $sort,
            'direction' => $direction,
        ];

        return $this->curl->get($url.'?'.http_build_query($data));
    }

    /**
     * Get a single pull request.
     *
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get(string $username, string $repo_name, int $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num]);

        return $this->curl->get($url);
    }

    /**
     * @param string $username
     * @param string $repo_name
     * @param int    $from_issue
     * @param string $title
     * @param string $head
     * @param string $base
     * @param string $body
     * @param bool   $maintainer_can_modify
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function create(string $username,
                           string $repo_name,
                           int $from_issue = 0,
                           string $title,
                           string $head,
                           string $base,
                           string $body = null,
                           bool $maintainer_can_modify = true)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, '/pulls']);

        $data = [
            'title' => $title,
            'body' => $body,
            'head' => $head,
            'base' => $base,
            'maintainer_can_modify' => $maintainer_can_modify,
        ];

        if (0 !== $from_issue) {
            array_shift($data);
            array_shift($data);

            $array['issue'] = $from_issue;
        }

        if ($this->is_update) {
            return $this->curl->patch($url, json_encode($data));
        }

        return $this->curl->post($url, json_encode($data));
    }

    /**
     * @param string      $username
     * @param string      $repo_name
     * @param int         $from_issue
     * @param string      $title
     * @param string      $head
     * @param string      $base
     * @param string|null $body
     * @param bool        $maintainer_can_modify
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function update(string $username,
                           string $repo_name,
                           int $from_issue = 0,
                           string $title,
                           string $head,
                           string $base,
                           string $body = null,
                           bool $maintainer_can_modify = true)
    {
        $this->is_update = true;
        $output = $this->create(...\func_get_args());
        $this->is_update = false;

        return $output;
    }

    /**
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listCommits(string $username, string $repo_name, int $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num, 'commits']);

        return $this->curl->get($url);
    }

    /**
     * List pull requests files.
     *
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listFiles(string $username, string $repo_name, int $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, '/pulls', $pr_num, '/files']);

        return $this->curl->get($url);
    }

    /**
     * Get if a pull request has been merged.
     *
     * @param string $username
     * @param string $repo_name
     * @param        $pr_num
     *
     * @return bool
     *
     * @throws Exception
     */
    public function isMerged(string $username, string $repo_name, $pr_num)
    {
        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num, 'merge']);

        $this->curl->get($url);

        $http_return_code = $this->curl->getCode();

        if (204 === $http_return_code) {
            return true;
        } elseif (404 === $http_return_code) {
            return false;
        }

        throw new Exception('pull_request is merged error', 500);
    }

    /**
     * @param string $username
     * @param string $repo_name
     * @param int    $pr_num
     * @param string $commit_title
     * @param string $commit_message
     * @param string $sha
     * @param int    $merge_method
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    public function merge(string $username,
                          string $repo_name,
                          int $pr_num,
                          string $commit_title,
                          ?string $commit_message,
                          string $sha,
                          int $merge_method)
    {
        switch ($merge_method) {
            case 1:
                $merge_method = 'merge';
                break;
            case 2:
                $merge_method = 'squash';
                break;
            case 3:
                $merge_method = 'rebase';
                break;
        }

        $url = $this->api_url.implode('/', ['/repos', $username, $repo_name, 'pulls', $pr_num, 'merge']);

        $data = [
            'commit_title' => $commit_title,
            'commit_message' => $commit_message ?? '',
            'sha' => $sha,
            'merge_method' => $merge_method,
        ];

        $output = $this->curl->put($url, json_encode($data));

        $http_return_code = $this->curl->getCode();

        if (200 === $http_return_code) {
            return true;
        }

        if (405 === $http_return_code) {
            throw new Exception('merge cannot be performed', 405);
        }

        if (409 === $http_return_code) {
            throw new Exception('sha was provided and pull request head did not match', 409);
        }

        throw new Exception($output, $http_return_code);
    }

    /**
     * List reviews on a pull request.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listReviews(string $repo_full_name, int $pull_number)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/reviews');
    }

    /**
     * Get a single review.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param int    $review_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getReview(string $repo_full_name, int $pull_number, int $review_id)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/reviews/'.$review_id);
    }

    /**
     * Delete a pending review.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param int    $review_id
     *
     * @throws Exception
     */
    public function deletePendingReview(string $repo_full_name, int $pull_number, int $review_id): void
    {
        $this->curl->delete($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/reviews/'.$review_id);
    }

    /**
     * Get comments for a single review.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param int    $review_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getCommentsForReview(string $repo_full_name, int $pull_number, int $review_id)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/reviews/'.$review_id.'/comments');
    }

    /**
     * Create a pull request review.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param string $commit_id
     * @param string $body
     * @param string $event
     * @param array  $comments       [ 'path'=>$path,'position'=>$position,'body'=>$comments_body ]
     *
     * @throws Exception
     */
    public function createReview(string $repo_full_name, int $pull_number, string $commit_id, string $body, string $event, array $comments): void
    {
        $data = [
            'commit_id' => $commit_id,
            'body' => $body,
            'event' => $event,
            'comments' => $comments,
        ];

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name, '/pulls/'.$pull_number.'/reviews', json_encode($data));
    }

    /**
     * Submit a pull request review.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param int    $review_id
     * @param string $body
     * @param string $event
     *
     * @throws Exception
     */
    public function submitReview(string $repo_full_name, int $pull_number, int $review_id, string $body, string $event): void
    {
        $data = [
            'body' => $body,
            'event' => $event,
        ];

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/reviews/'.$review_id.'/events', json_encode($data));
    }

    /**
     * Dismiss a pull request review.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param int    $review_id
     * @param string $message
     *
     * @throws Exception
     */
    public function dismissReview(string $repo_full_name, int $pull_number, int $review_id, string $message): void
    {
        $this->curl->put($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/reviews/'.$review_id.'/dismissals', json_encode([
                    'message' => $message,
                ]
            )
        );
    }

    /**
     * List comments on a pull request.
     *
     * @param string $repo_full_name repo full name
     * @param int    $pull_number
     * @param string $sort           created or updated
     * @param string $direction      asc or desc
     * @param string $since
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listComments(string $repo_full_name, int $pull_number, string $sort = 'created', ?string $direction, ?string $since)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/comments?'.http_build_query([
                    'sort' => $sort,
                    'direction' => $direction,
                    'since' => $since,
                ]
            )
        );
    }

    /**
     * List comments in a repository.
     *
     * @param string $repo_full_name repo full name
     * @param string $sort           created or updated
     * @param string $direction      asc or desc
     * @param string $since
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function ListCommentsInRepository(string $repo_full_name, string $sort, string $direction, string $since)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/comments?'.http_build_query([
                    'sort' => $sort,
                    'direction' => $direction,
                    'since' => $since,
                ]
            )
        );
    }

    /**
     * Get a single comment.
     *
     * @param string $repo_full_name
     * @param int    $comment_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getComment(string $repo_full_name, int $comment_id)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/comments/'.$comment_id);
    }

    /**
     * Create a comment.
     *
     * @param string   $repo_full_name
     * @param int      $pull_number
     * @param string   $body
     * @param string   $commit_id
     * @param string   $path
     * @param string   $position
     * @param int|null $in_reply_to
     *
     * @throws Exception
     */
    public function createComment(string $repo_full_name,
                                  int $pull_number,
                                  string $body,
                                  string $commit_id,
                                  string $path,
                                  string $position,
                                  ?int $in_reply_to): void
    {
        $data = [
            'body' => $body,
            'commit_id' => $commit_id,
            'path' => $path,
            'position' => $position,
            'in_reply_to' => $in_reply_to,
        ];

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/comments', json_encode($data));
    }

    /**
     * Edit a comment.
     *
     * @param string $repo_full_name
     * @param int    $comment_id
     * @param string $body
     *
     * @throws Exception
     */
    public function editComment(string $repo_full_name, int $comment_id, string $body): void
    {
        $this->curl->patch($this->api_url.'/repos/'.$repo_full_name.'/pulls/comments/'.$comment_id, [
                'body' => $body,
            ]
        );
    }

    /**
     * Delete a comment.
     *
     * 204
     *
     * @param string $repo_full_name
     * @param int    $comment_id
     *
     * @throws Exception
     */
    public function deleteComment(string $repo_full_name, int $comment_id): void
    {
        $this->curl->delete($this->api_url.'/repos/'.$repo_full_name.'/pulls/comments/'.$comment_id);
    }

    /**
     * List review requests.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     *
     * @throws Exception
     */
    public function listReviewRequests(string $repo_full_name, int $pull_number): void
    {
        $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/requested_reviewers');
    }

    /**
     * Create a review request.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param array  $reviewers
     * @param array  $team_reviewers
     *
     * @throws Exception
     */
    public function createReviewRequest(string $repo_full_name, int $pull_number, array $reviewers, array $team_reviewers): void
    {
        $data = [
            'reviewers' => $reviewers,
            'team_reviewers' => $team_reviewers,
        ];

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/requested_reviewers', json_encode($data));
    }

    /**
     * Delete a review request.
     *
     * @param string $repo_full_name
     * @param int    $pull_number
     * @param array  $reviewers
     * @param array  $team_reviewers
     *
     * @throws Exception
     */
    public function deleteReviewRequest(string $repo_full_name, int $pull_number, array $reviewers, array $team_reviewers): void
    {
        $data = [
            'reviewers' => $reviewers,
            'team_reviewers' => $team_reviewers,
        ];

        $this->curl->delete($this->api_url.'/repos/'.$repo_full_name.'/pulls/'.$pull_number.'/requested_reviewers', json_encode($data));
    }
}

<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Gist;

use PCIT\GitHub\Service\CICommon;

class CommentsClient
{
    use CICommon;

    /**
     * List comments on a gist.
     *
     * @param string $gist_id
     *
     * @return mixed
     *
     * @throws \Exception
     *
     * @see https://developer.github.com/v3/gists/comments/#list-comments-on-a-gist
     */
    public function list(string $gist_id)
    {
        return $this->curl->get($this->api_url.'/gists/'.$gist_id.'/comments');
    }

    /**
     * Get a single comment.
     *
     * @param string $gist_id
     * @param int    $comment_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function get(string $gist_id, int $comment_id)
    {
        return $this->curl->get($this->api_url.'/gists/'.$gist_id.'/comments/'.$comment_id);
    }

    /**
     * Create a comment.
     *
     * 201
     *
     * @param string $gist_id
     * @param string $body
     * @param string $method
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function create(string $gist_id, string $body, string $method = 'post')
    {
        return $this->curl->$method($this->api_url.'/gists/'.$gist_id.'/comments', json_encode(['body' => $body]));
    }

    /**
     * Edit a comment.
     *
     * @param string $gist_id
     * @param string $body
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function edit(string $gist_id, string $body)
    {
        return $this->create($gist_id, $body, 'patch');
    }

    /**
     * Delete a comment.
     *
     * 204
     *
     * @param string $gist_id
     * @param int    $comment_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function delete(string $gist_id, int $comment_id)
    {
        return $this->curl->delete($this->api_url.'/gists/'.$gist_id.'/comments/'.$comment_id);
    }
}

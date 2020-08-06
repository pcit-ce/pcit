<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Gist;

use PCIT\GPI\ServiceClientCommon;

class CommentsClient
{
    use ServiceClientCommon;

    /**
     * List comments on a gist.
     *
     * @throws \Exception
     *
     * @return mixed
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
     * @throws \Exception
     *
     * @return mixed
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
     * @throws \Exception
     *
     * @return mixed
     */
    public function create(string $gist_id, string $body, string $method = 'post')
    {
        return $this->curl->$method($this->api_url.'/gists/'.$gist_id.'/comments', json_encode(['body' => $body]));
    }

    /**
     * Edit a comment.
     *
     * @throws \Exception
     *
     * @return mixed
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
     * @throws \Exception
     *
     * @return mixed
     */
    public function delete(string $gist_id, int $comment_id)
    {
        return $this->curl->delete($this->api_url.'/gists/'.$gist_id.'/comments/'.$comment_id);
    }
}

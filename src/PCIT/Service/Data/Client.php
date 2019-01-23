<?php

declare(strict_types=1);

namespace PCIT\Service\GitHub\Data;

use PCIT\Service\CICommon;

class Client
{
    use CICommon;

    /**
     * Get a blob.
     *
     * @param string $repo_full_name
     * @param string $file_sha
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getBlobs(string $repo_full_name, string $file_sha)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/blobs/'.$file_sha);
    }

    /**
     * Create a blob.
     *
     * 201
     *
     * @param string $repo_full_name repo full name
     * @param string $content        the new blob's content
     * @param string $encoding       utf-8 or base64
     *
     * @throws \Exception
     */
    public function createBlobs(string $repo_full_name, string $content, string $encoding = 'utf-8'): void
    {
        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/git/blobs', json_encode([
            'content' => $content,
            'encoding' => $encoding,
        ]));
    }

    /**
     * Get a commit.
     *
     * @param string $repo_full_name
     * @param string $sha
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getCommits(string $repo_full_name, string $sha)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/commits/'.$sha);
    }

    /**
     * Create a commit.
     *
     * 201
     *
     * @param string $repo_full_name
     * @param string $commit_message
     * @param string $tree
     * @param array  $parents
     * @param string $committer_name
     * @param string $committer_email
     * @param string $date
     * @param string $signature
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function createCommit(string $repo_full_name,
                                 string $commit_message,
                                 string $tree,
                                 array $parents,
                                 string $committer_name,
                                 string $committer_email,
                                 string $date,
                                 string $signature)
    {
        $data = [
            'message' => $commit_message,
            'parents' => $parents,
            'tree' => $tree,
            'signature' => $signature,
        ];

        if ($committer_name) {
            $data = array_merge($data, [
                'committer' => [
                    'name' => $committer_name,
                    'email' => $committer_email,
                    'data' => $date,
                ],
            ]);
        }

        return $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/git/commits', json_encode($data));
    }

    /**
     * Commit signature verification.
     *
     * @param string $repo_full_name
     * @param string $sha
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function commitSignatureVerification(string $repo_full_name, string $sha)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/commits/'.$sha);
    }

    /**
     * Get a reference.
     *
     * @param string $repo_full_name repo full name
     * @param string $ref            heads/skunkworkz/featureA
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getRef(string $repo_full_name, string $ref)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/refs/'.$ref);
    }

    /**
     * Get all references.
     *
     * @param string $repo_full_name
     * @param bool   $tags
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function allRef(string $repo_full_name, bool $tags = false)
    {
        if ($tags) {
            return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/refs/tags');
        }

        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/refs');
    }

    /**
     * Create a reference.
     *
     * 201
     *
     * @param string $repo_full_name repo full name
     * @param string $ref            heads/master
     * @param string $sha
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function createRef(string $repo_full_name, string $ref, string $sha)
    {
        $data = [
            'ref' => 'refs/'.$ref,
            'sha' => $sha,
        ];

        return $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/git/refs', json_encode($data));
    }

    /**
     * Update a reference.
     *
     * @param string $repo_full_name
     * @param string $ref
     * @param string $sha
     * @param bool   $force
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function updateRef(string $repo_full_name, string $ref, string $sha, bool $force)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/git/refs/'.$ref;

        $data = [
            'sha' => $sha,
            'force' => $force,
        ];

        return $this->curl->patch($url, json_encode($data));
    }

    /**
     * Delete a reference.
     *
     * 204
     *
     * @param string $repo_full_name repo full name
     * @param string $ref            heads/feature-a tags/v1.0
     *
     * @throws \Exception
     */
    public function deleteRef(string $repo_full_name, string $ref): void
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/git/refs/'.$ref;

        $this->curl->delete($url);
    }

    /**
     * Get a tag.
     *
     * @param string $repo_full_name
     * @param        $tag_sha
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getTag(string $repo_full_name, $tag_sha)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/tags/'.$tag_sha);
    }

    /**
     * Create a tag object.
     *
     * 201
     *
     * @param string      $repo_full_name
     * @param string      $tag_name
     * @param string      $tag_message
     * @param string      $object
     * @param string      $type
     * @param string|null $committer_name
     * @param string|null $committer_email
     * @param string|null $date
     *
     * @throws \Exception
     */
    public function createTag(string $repo_full_name,
                              string $tag_name,
                              string $tag_message,
                              string $object,
                              string $type = 'commit',
                              ?string $committer_name,
                              ?string $committer_email,
                              ?string $date): void
    {
        $data = [
            'tag' => $tag_name,
            'message' => $tag_message,
            'object' => $object,
            'type' => $type,
        ];

        if ($committer_name) {
            $data = array_merge($data, [
                'tagger' => [
                    'name' => $committer_name,
                    'email' => $committer_email,
                    'date' => $date,
                ],
            ]);
        }

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/git/tags', json_encode($data));
    }

    /**
     * Tag signature verification.
     *
     * @param string $repo_full_name
     * @param string $tag_sha
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function tagSignatureVerification(string $repo_full_name, string $tag_sha)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/tags/'.$tag_sha);
    }

    /**
     * Get a tree.
     *
     * @param string $repo_full_name
     * @param string $tree_sha
     * @param bool   $recursively
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getTree(string $repo_full_name, string $tree_sha, bool $recursively = false)
    {
        if ($recursively) {
            return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/trees/'.$tree_sha.'?recursive=1');
        }

        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/git/trees/'.$tree_sha);
    }

    /**
     * Create a tree.
     *
     * 201
     *
     * @param string $repo_full_name repo full name
     * @param        $base_tree
     * @param array  $tree           [['path'='file.name','mode'=>'100644','type'=>'blob','sha'=>$sha]]
     *
     * @throws \Exception
     */
    public function createTree(string $repo_full_name,
                               $base_tree,
                               array $tree): void
    {
        $data = [
            'base_tree' => $base_tree,
            'tree' => $tree,
        ];

        $this->curl->post($this->api_url.'/repos/'.$repo_full_name.'/git/trees', json_encode($data));
    }
}

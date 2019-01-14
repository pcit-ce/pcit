<?php

declare(strict_types=1);

namespace PCIT\Deployer\Adapter;

class GitHubPages extends AbstractAdapter
{
    const PROVIDER = 'pages_github';
    public $username = 'pcit';
    public $target_branch = 'gh-pages';
    public $git_url;
    public $local_dir = 'public';
    public $email = 'ci@khs1994.com';
    public $keep_history = false;
    public $git_token;

    public function __construct(array $config)
    {
        $this->username = $config['username'] ?? 'pcit';
        $this->target_branch = $config['target_branch'] ?? 'gh-pages';
        $this->git_url = $config['git_url'] ?? null;
        $this->local_dir = $config['local_dir'] ?? 'public';
        $this->email = $config['email'] ?? 'ci@khs1994.com';
        $this->keep_history = $config['keep_history'] ?? false;
        $this->git_token = $config['git_token'] ?? null;
    }

    public function deploy()
    {
        return [
            'image' => 'pcit/pages',
            'env' => [
             'PCIT_USERNAME='.$this->username,
             'PCIT_TARGET_BRANCH='.$this->target_branch,
             'PCIT_GIT_URL='.$this->git_url,
             'PCIT_LOCAL_DIR='.$this->local_dir,
             'PCIT_EMAIL='.$this->email,
             'PCIT_KEEP_HISTORY='.$this->keep_history,
             'PCIT_GIT_TOKEN='.$this->git_token,
         ],
     ];
    }
}

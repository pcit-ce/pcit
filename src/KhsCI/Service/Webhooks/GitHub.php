<?php

declare(strict_types=1);

namespace KhsCI\Service\Webhooks;

use KhsCI\Support\HTTP;
use Exception;

class GitHub
{
    protected $secret;

    public function __construct($config)
    {
        $this->secret = $config['secret'];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function check()
    {
        $headers = HTTP::getAllHeaders();

        $signature = $headers('X-Hub-Signature');
        $type = $headers('X-GitHub-Event');
        $content = file_get_contents('php://input');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $this->secret);

        if ($github_hash === $serverHash) {
            return [$type, $content];
        }

        throw new \Exception('');
    }

    /**
     * @throws Exception
     */
    public function deploy()
    {
        $content = $this->check();
        file_put_contents(sys_get_temp_dir().DIRECTORY_SEPARATOR.session_create_id(), $content['content']);
    }

    public function setRepo($repo, $branch, $path, $script)
    {

    }
}

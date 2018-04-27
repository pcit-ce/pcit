<?php

declare(strict_types=1);

namespace KhsCI\Service\Webhooks;

use Error;
use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\Request;

class GitHub
{
    /**
     * @throws Exception
     *
     * @return array
     */
    public function __invoke()
    {
        $signature = Request::getHeader('X-Hub-Signature');
        $type = Request::getHeader('X-Github-Event') ?? 'undefined';
        $content = file_get_contents('php://input');
        $secret = Env::get('WEBHOOKS_TOKEN') ?? md5('khsci');

        list($algo, $github_hash) = explode('=', $signature, 2);

        $serverHash = hash_hmac($algo, $content, $secret);

        // return $this->$type($content);

        if ($github_hash === $serverHash) {
            try {
                return $this->$type($content);
            } catch (Error | Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }

        throw new \Exception('', 402);
    }

    /**
     * @throws Exception
     */
    public function deploy(): void
    {
        $content = $this->check();
        file_put_contents(sys_get_temp_dir().DIRECTORY_SEPARATOR.session_create_id(), $content['content']);
    }

    public function setRepo($repo, $branch, $path, $script): void
    {
    }

    public function ping($content)
    {
        $pdo = DB::connect();

        $sql = <<<EOF
INSERT builds(request_raw) VALUES('$content');
EOF;
        $pdo->exec($sql);
    }
}

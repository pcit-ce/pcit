<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Repo;

class ShowStatusController
{
    /**
     * @param mixed ...$arg
     *
     * @throws \Exception
     */
    @@\Route('get', '{git_type}/{username}/{repo_name}/status')
    @@\Route('get', 'api/repo/{git_type}/{username}/{repo_name}/status')
    public function __invoke(...$arg)
    {
        /** @var \PCIT\Framework\Http\Request */
        $request = app('request');
        // $branch = $_GET['branch'] ?? null;
        $branch = $request->query->get('branch');

        list($git_type, $username, $repo) = $arg;

        $arg = [$username, $repo, $git_type];

        if (!$branch) {
            $branch = Repo::getDefaultBranch(...$arg) ?? 'master';
        }

        $rid = Repo::getRid(...$arg);

        $status = Build::getLastBuildStatus((int) $rid, $branch);

        $svg = null === $status ?
            'public/ico/unknown.svg'
            : 'public/ico/'.$status.'.svg';

        //$ts = gmdate('D, d M Y H:i:s', time() + 300).' GMT';
        // ini_set('expose_php', 'Off');

        //header_remove('x-powered-by');

        $response = \Response::file(base_path($svg),[
            'X-Powered-By' => 'PCIT https://ci.khs1994.com',
            'X-PCIT-Author' => 'https://khs1994.com',
            'Content-Type' => 'image/svg+xml;charset=utf-8',
        ]);

        $expires = (new \DateTime())
        ->setTimestamp(time() + 300);

        return $response->setExpires($expires) // 'Expires' => $ts
        ->setCache([
            "max_age" => 300,
            "public" => true,
        ]); // 'Cache-Control' => 'max-age=300,public'
    }

    /**
     * @param mixed ...$arg
     *
     * @return string
     */
    @@\Route('get', '{git_type}/{username}/{repo_name}/getstatus')
    public function getStatus(...$arg)
    {
        list($git_type, $username, $repo) = $arg;
        $host = config('app.host');

        return <<<EOF
<pre>

<h1>IMAGE</h1>

$host/$git_type/$username/$repo/status?branch=master

<h1>MARKDOWN</h1>

[![Build Status]($host/$git_type/$username/$repo/status?branch=master)]($host/$git_type/$username/$repo)

</pre>
EOF;
    }
}

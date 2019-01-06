<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Repo;
use Exception;
use PCIT\Support\Env;
use PCIT\Support\Response;

class ShowStatusController
{
    /**
     * @param mixed ...$arg
     *
     * @throws Exception
     */
    public function __invoke(...$arg)
    {
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
            file_get_contents(__DIR__.'/../../../../public/ico/unknown.svg')
        : file_get_contents(__DIR__.'/../../../../public/ico/'.$status.'.svg');

        $ts = gmdate('D, d M Y H:i:s', time() + 300).' GMT';
        // ini_set('expose_php', 'Off');

        header_remove('x-powered-by');

        return new Response($svg, 200, [
            'Expires' => $ts,
            'Content-Type' => 'image/svg+xml;charset=utf-8',
            'Cache-Control' => 'max-age=300,public',
            'X-Powered-By' => 'pcit https://ci.khs1994.com',
            'X-PCIT-Author' => 'https://khs1994.com',
            // no-cache
        ]);
    }

    /**
     * @param mixed ...$arg
     *
     * @return string
     */
    public function getStatus(...$arg)
    {
        list($git_type, $username, $repo) = $arg;
        $host = env('CI_HOST');

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

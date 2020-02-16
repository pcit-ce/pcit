<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use PCIT\Framework\Http\Request;

class OpenAPI
{
    public function __invoke(Request $request)
    {
        $yaml = file_get_contents(base_path().'/openapi/openapi.yaml');

        $is_coding = $request->get('coding');

        $ci_host = env('CI_HOST').'/api';

        if ('true' === $is_coding) {
            $yaml .= <<<EOF
servers:
- url: 'https://ci.khs1994.com/api'
  description: PCIT-CE API
- url: $ci_host
  description: PCIT-EE API
EOF;
        } else {
            $yaml .= <<<EOF
servers:
- url: 'https://ci.khs1994.com/api'
  description: PCIT-CE API
- url: 'https://{pcit-url}/api'
  description: PCIT-EE API
  variables:
  pcit-url:
  description: your pcit ee url
  default: ci.khs1994.com
EOF;
        }

        return \Response::make($yaml, 200, [
            'application/yaml',
        ]);
    }
}

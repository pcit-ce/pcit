<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use PCIT\Framework\Http\Request;
use PCIT\Framework\Support\Response;

class OpenAPI
{
    public function __invoke(Request $request)
    {
        $json = file_get_contents(base_path().'/openapi/openapi.json');

        $is_coding = $request->get('coding');

        if ('true' === $is_coding) {
            $is_json = false;
            $json = json_decode($json, true);
            $json['servers'][1] = [
              'url' => 'https://pcit-url/api',
              'description' => 'PCIT EE API',
          ];
        }

        return Response::json($json, $is_json ?? true);
    }
}

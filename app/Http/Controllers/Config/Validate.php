<?php

declare(strict_types=1);

namespace App\Http\Controllers\Config;

use JsonSchema\Validator;
use PCIT\Framework\Http\Request;
use Symfony\Component\Yaml\Yaml;

class Validate
{
    public function __invoke(Request $request)
    {
        /** @var string */
        $pcit_config = $request->getContent();

        $is_json = false;
        if ('application/json' === strtolower($request->getContentType() ?? '')) {
            $is_json = true;
        }

        $data = json_decode($is_json ? $pcit_config : json_encode(Yaml::parse($pcit_config)));

        $validator = new Validator();
        $validator->validate($data,
      (object) ['$ref' => 'file://'.realpath(base_path().'config/config_schema.json')]);

        if ($validator->isValid()) {
            return \Response::make('ok');
        } else {
            $message = [];

            foreach ($validator->getErrors() as $error) {
                $message[] = [$error['property'] => $error['message']];
            }

            return \Response::json($message);
        }
    }
}

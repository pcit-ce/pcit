<?php

declare(strict_types=1);

namespace App\Http\Controllers\Config;

use JsonSchema\Constraints\BaseConstraint;
use JsonSchema\Validator;
use PCIT\Framework\Attributes\Route;
use PCIT\Framework\Http\Request;
use Symfony\Component\Yaml\Yaml;

class Validate
{
    #[Route('post', 'validate')]
    #[Route('post', 'api/validate')]
    public function __invoke(Request $request)
    {
        /** @var string */
        $pcit_config = $request->getContent();

        if ('application/json' === strtolower($request->getContentType() ?? '')) {
            $data = json_decode($pcit_config);
        } else {
            $data = BaseConstraint::arrayToObjectRecursive(Yaml::parse($pcit_config));
        }

        $validator = new Validator();
        $validator->validate(
            $data,
            (object) ['$ref' => 'file://'.realpath(base_path('config/config.schema.json'))]
        );

        if ($validator->isValid()) {
            return \Response::make('ok');
        }
        $message = [];

        foreach ($validator->getErrors() as $error) {
            $message[] = [$error['property'] => $error['message']];
        }

        return \Response::json($message);
    }
}

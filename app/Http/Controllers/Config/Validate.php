<?php

declare(strict_types=1);

namespace App\Http\Controllers\Config;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Config\Validator as ConfigValidator;
use PCIT\Framework\Attributes\Route;
use PCIT\Framework\Http\Request;
use Symfony\Component\Yaml\Yaml;

class Validate
{
    #[Route('post', 'validate')]
    #[Route('post', 'api/validate')]
    public function __invoke(Request $request, ConfigValidator $validator)
    {
        /** @var string */
        $pcit_config = $request->getContent();

        if ('application/json' === strtolower($request->getContentType() ?? '')) {
            $data = json_decode($pcit_config);
        } else {
            $data = BaseConstraint::arrayToObjectRecursive(Yaml::parse($pcit_config));
        }

        $result = $validator->validate($data);

        if ([] === $result) {
            return \Response::make('');
        }

        $response = \Response::json($result);

        $response->setStatusCode(400);

        return $response;
    }
}

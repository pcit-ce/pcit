<?php

declare(strict_types=1);

namespace PCIT\Config;

use JsonSchema\Validator as JsonSchemaValidator;

class Validator
{
    public function validate($object)
    {
        $data = $object;

        $validator = new JsonSchemaValidator();
        $validator->validate(
            $data,
            (object) ['$ref' => 'file://'.realpath(base_path('config/config.schema.json'))]
        );

        if ($validator->isValid()) {
            return [];
        }

        $result = [];

        foreach ($validator->getErrors() as $error) {
            $property = $error['property'];
            $message = $error['message'];

            $result[] = compact('property', 'message');
        }

        return $result;
    }
}

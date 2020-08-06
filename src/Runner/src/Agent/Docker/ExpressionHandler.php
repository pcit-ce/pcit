<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker;

class ExpressionHandler
{
    public function handleOutput(string $string, array $outputs): string
    {
        $expressions = [];

        $result = preg_match_all('/(\${{).*(}})/U', $string, $expressions);

        if (!$result) {
            return $string;
        }

        foreach ($expressions[0] as $k) {
            $expression = explode('.', trim(trim(substr($k, 3), '}')));

            if ('steps' === $expression[0] && 'outputs' === $expression[2] && ($expression[3] ?? false)) {
                $result = $outputs[$expression[1]][$expression[3]] ?? null;

                $string = str_replace($k, $result, $string);

                continue;
            }

            if ('steps' === $expression[0] && 'outputs' === $expression[2]) {
                $result = json_encode($outputs[$expression[1]] ?? null);
            }

            $string = str_replace($k, $result, $string);
        }

        return $string;
    }
}

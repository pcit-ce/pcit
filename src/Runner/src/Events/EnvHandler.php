<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

class EnvHandler
{
    public function arrayHandler($value)
    {
        if (\is_string($value)) {
            return $value;
        }
        $new_value = null;
        foreach ($value as $k) {
            $new_value .= $k.',';
        }

        return rtrim($new_value, ',');
    }
}

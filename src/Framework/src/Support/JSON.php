<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

class JSON
{
    /**
     * @param string $json
     *
     * @return string
     *
     * @author www.veryhuo.com
     *
     * @deprecated please use JSON_PRETTY_PRINT
     * @see    http://www.veryhuo.com/a/view/50222.html
     */
    public static function beautiful(?string $json, string $indentStr = '  ')
    {
        if (null === $json) {
            return null;
        }

        $result = '';
        $pos = 0;
        $strLen = \strlen($json);

        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; ++$i) {
            // Grab the next character in the string.
            $char = substr($json, $i, 1);
            // Are we inside a quoted string?
            if ('"' === $char && '\\' !== $prevChar) {
                $outOfQuotes = !$outOfQuotes;
            // If this character is the end of an element,
                // output a new line and indent the next line.
            } elseif (('}' === $char || ']' === $char) && $outOfQuotes) {
                $result .= $newLine;
                --$pos;
                for ($j = 0; $j < $pos; ++$j) {
                    $result .= $indentStr;
                }
            }
            // Add the character to the result string.
            $result .= $char;
            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if ((',' === $char || '{' === $char || '[' === $char) && $outOfQuotes) {
                $result .= $newLine;
                if ('{' === $char || '[' === $char) {
                    ++$pos;
                }
                for ($j = 0; $j < $pos; ++$j) {
                    $result .= $indentStr;
                }
            }
            $prevChar = $char;
        }

        return $result;
    }
}
